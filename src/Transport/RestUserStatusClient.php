<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Transport;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface as HttpClient;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\TId\UserStatusClient;
use Yiisoft\Http\Method;

final readonly class RestUserStatusClient implements UserStatusClient
{
    public function __construct(
        private HttpClient $client,
        private Serializer $serializer,
    ) {
    }

    public function getForeignAgentStatus(string $accessToken): bool
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/foreignagent/status',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        /** @var bool $result */
        $result = $this->serializer->deserialize($response, 'bool', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[isForeignAgent]',
        ]);

        return $result;
    }

    public function getBanksBlackListStatus(string $accessToken): bool
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/blacklist/status',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        /** @var bool $result */
        $result = $this->serializer->deserialize($response, 'bool', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[isBlacklisted]',
        ]);

        return $result;
    }

    public function getIdentificationStatus(string $accessToken): bool
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/identification/status',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        /** @var bool $result */
        $result = $this->serializer->deserialize($response, 'bool', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[isIdentified]',
        ]);

        return $result;
    }

    public function getPublicOfficialPersonStatus(string $accessToken): bool
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/pdl/status',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        /** @var bool $result */
        $result = $this->serializer->deserialize($response, 'bool', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[isPublicOfficialPerson]',
        ]);

        return $result;
    }
}
