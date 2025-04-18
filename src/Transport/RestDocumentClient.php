<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Transport;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface as HttpClient;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer as Normalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Vanta\Integration\TId\DocumentClient;
use Vanta\Integration\TId\Response\Document;
use Vanta\Integration\TId\Response\InnNumber;
use Vanta\Integration\TId\Response\SnilsNumber;
use Vanta\Integration\TId\Struct\Address;
use Vanta\Integration\TId\Struct\AddressType;
use Vanta\Integration\TId\Struct\DocumentType;
use Yiisoft\Http\Method;

final readonly class RestDocumentClient implements DocumentClient
{
    public function __construct(
        private HttpClient $client,
        private Serializer $serializer,
    ) {
    }

    public function getDocument(string $accessToken, DocumentType $documentType = DocumentType::PASSPORT, Uuid $requestId = new UuidV7()): Document
    {
        $request = new Request(
            Method::GET,
            '/openapi/api/v1/individual/documents/passport?' . http_build_query(['idType' => $documentType->value]),
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request);

        $responseContent = $response->getBody()->getContents();

        return $this->serializer->deserialize($responseContent, Document::class, 'json', [
            Normalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                Document::class => ['rawValue' => $responseContent],
            ],
        ]);
    }

    public function getAddress(string $accessToken, ?AddressType $addressType = null, Uuid $requestId = new UuidV7()): array
    {
        $uri = '/openapi/api/v1/individual/addresses';

        if (null != $addressType) {
            $uri .= '?' . http_build_query(['addressType' => $addressType->value]);
        }

        $request = new Request(
            Method::GET,
            $uri,
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        /** @var list<Address> $result */
        $result = $this->serializer->deserialize($response, Address::class . '[]', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[addresses]',
        ]);

        return $result;
    }

    public function getPassportCheckSmevResult(string $accessToken, Uuid $requestId = new UuidV7()): bool
    {
        $request = new Request(
            Method::POST,
            '/openapi/api/v1/individual/documents/passport-check-smev4',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'X-Request-Id'  => $requestId->jsonSerialize(),
            ],
        );

        $response = $this->client->sendRequest($request)->getBody()->__toString();

        return 'VALID' == $this->serializer->deserialize($response, 'string', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[result]',
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

        return $this->serializer->deserialize($response, InnNumber::class, 'json');
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

        $response = $this->client->sendRequest($request);

        $responseContent = $response->getBody()->getContents();

        return $this->serializer->deserialize($responseContent, SnilsNumber::class, 'json');
    }
}
