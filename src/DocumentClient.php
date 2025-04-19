<?php

declare(strict_types=1);

namespace Vanta\Integration\TId;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Vanta\Integration\TId\Response\Document;
use Vanta\Integration\TId\Response\InnNumber;
use Vanta\Integration\TId\Response\SnilsNumber;
use Vanta\Integration\TId\Struct\Address;
use Vanta\Integration\TId\Struct\AddressType;
use Vanta\Integration\TId\Struct\DocumentType;

interface DocumentClient
{
    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getDocument(string $accessToken, DocumentType $documentType = DocumentType::PASSPORT, Uuid $requestId = new UuidV7()): Document;

    /**
     * @param non-empty-string $accessToken
     *
     * @return list<Address>
     *
     * @throws ClientException
     */
    public function getAddress(string $accessToken, ?AddressType $addressType = null, Uuid $requestId = new UuidV7()): array;

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getPassportCheckSmevResult(string $accessToken, Uuid $requestId = new UuidV7()): bool;

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getInn(string $accessToken, Uuid $requestId = new UuidV7()): InnNumber;

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getSnils(string $accessToken, Uuid $requestId = new UuidV7()): SnilsNumber;
}
