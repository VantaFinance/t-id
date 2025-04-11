<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Transport;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface as HttpClient;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer as Normalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Vanta\Integration\TId\BusinessClient;
use Vanta\Integration\TId\Response\DocumentInfo;
use Vanta\Integration\TId\Struct\Address;
use Vanta\Integration\TId\Struct\AdressType;
use Vanta\Integration\TId\Struct\DocumentType;
use Vanta\Integration\TId\Struct\InnNumber;
use Vanta\Integration\TId\Struct\SnilsNumber;
use Yiisoft\Http\Method;

final readonly class RestBusinessClient implements BusinessClient
{
    public function __construct(
        private HttpClient $client,
        private Serializer $serializer,
    ) {
    }

    public function getDocumentInfo(string $accessToken, DocumentType $documentType = DocumentType::PASSPORT, Uuid $requestId = new UuidV7()): DocumentInfo
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/documents/passport?' . http_build_query(['idType' => $documentType]),
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($response, DocumentInfo::class, 'json', [
            Normalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                DocumentInfo::class => ['raw_value' => $response],
            ],
        ]);
    }

    public function getAdressInfo(string $accessToken, AdressType $adressType = AdressType::REGISTRATION_ADDRESS, Uuid $requestId = new UuidV7()): array
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/addresses?' . http_build_query(['addressType' => $adressType]),
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        /** @var list<Address> $result */
        $result = $this->serializer->deserialize($response, Address::class . '[]', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => 'addresses',
        ]);

        return $result;
    }

    public function getPassportCheckSmevResult(string $accessToken, Uuid $requestId = new UuidV7()): bool
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/documents/passport-check-smev4',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return 'VALID' == $this->serializer->deserialize($response, 'string', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => 'result',
        ]);
    }

    public function getInn(string $accessToken, Uuid $requestId = new UuidV7()): InnNumber
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/documents/inn',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($response, InnNumber::class, 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => 'inn',
        ]);
    }

    public function getSnils(string $accessToken, Uuid $requestId = new UuidV7()): SnilsNumber
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/documents/snils',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($response, SnilsNumber::class, 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => 'snils',
        ]);
    }

    public function getForeignAgent(string $accessToken): bool
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
            UnwrappingDenormalizer::UNWRAP_PATH => 'isForeignAgent',
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
            UnwrappingDenormalizer::UNWRAP_PATH => 'isBlacklisted',
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
            UnwrappingDenormalizer::UNWRAP_PATH => 'isIdentified',
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
            UnwrappingDenormalizer::UNWRAP_PATH => 'isPublicOfficialPerson',
        ]);

        return $result;
    }
}
