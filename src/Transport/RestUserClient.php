<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Transport;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface as HttpClient;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer as Normalizer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\Response\AuthIntrospect;
use Vanta\Integration\TId\Response\PairKey;
use Vanta\Integration\TId\Response\User;
use Vanta\Integration\TId\UserClient;
use Yiisoft\Http\Method;

final readonly class RestUserClient implements UserClient
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

    public function getUser(string $accessToken): User
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

        return $this->serializer->deserialize($response, User::class, 'json', [
            Normalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                User::class => ['rawValue' => $response],
            ],
        ]);
    }

    public function getAuthIntrospect(string $accessToken): AuthIntrospect
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

        return $this->serializer->deserialize($response, AuthIntrospect::class, 'json');
    }
}
