<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Transport;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface as HttpClient;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer as Normalizer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\TId\IdClient;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\Response\AuthIntrospectInfo;
use Vanta\Integration\TId\Response\PairKey;
use Vanta\Integration\TId\Response\UserInfo;
use Yiisoft\Http\Method;

final readonly class RestIdClient implements IdClient
{
    public function __construct(
        private HttpClient $client,
        private Serializer $serializer,
        private ConfigurationClient $configurationClient,
    ) {
    }

    public function getPairKeyByAuthorizationCode(string $code, string $redirectUri): PairKey
    {
        $requestData = [
            'grant_type'   => 'authorization_code',
            'redirect_uri' => $redirectUri,
            'code'         => $code,
        ];

        $request = new Request(
            Method::POST,
            '/auth/token',
            [
                'Authorization' => 'Basic ' . base64_encode($this->configurationClient->clientId . ':' . $this->configurationClient->clientSecret),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            http_build_query($requestData),
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($response, PairKey::class, 'json');
    }

    public function getUserInfo(string $accessToken): UserInfo
    {
        $requestData = [
            'client_id'     => $this->configurationClient->clientId,
            'client_secret' => $this->configurationClient->clientSecret,
        ];

        $request = new Request(
            Method::POST,
            '/userinfo/userinfo',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            http_build_query($requestData),
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($response, UserInfo::class, 'json', [
            Normalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                UserInfo::class => ['rawValue' => $response],
            ],
        ]);
    }

    public function getAuthIntrospectInfo(string $accessToken): AuthIntrospectInfo
    {
        $request = new Request(
            Method::POST,
            '/auth/introspect',
            [
                'Authorization' => 'Basic ' . base64_encode($this->configurationClient->clientId . ':' . $this->configurationClient->clientSecret),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            http_build_query(['token' => $accessToken]),
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($response, AuthIntrospectInfo::class, 'json');
    }
}
