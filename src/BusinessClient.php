<?php

declare(strict_types=1);

namespace Vanta\Integration\TId;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Vanta\Integration\TId\Response\DocumentInfo;
use Vanta\Integration\TId\Struct\Address;
use Vanta\Integration\TId\Struct\AdressType;
use Vanta\Integration\TId\Struct\DocumentType;
use Vanta\Integration\TId\Struct\InnNumber;
use Vanta\Integration\TId\Struct\SnilsNumber;

interface BusinessClient
{
    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getDocumentInfo(string $accessToken, DocumentType $documentType = DocumentType::PASSPORT, Uuid $requestId = new UuidV7()): DocumentInfo;

    /**
     * @param non-empty-string $accessToken
     *
     * @return list<Address>
     *
     * @throws ClientException
     */
    public function getAdressInfo(string $accessToken, AdressType $adressType = AdressType::REGISTRATION_ADDRESS, Uuid $requestId = new UuidV7()): array;

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

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getForeignAgent(string $accessToken): bool;

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getBanksBlackListStatus(string $accessToken): bool;

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getIdentificationStatus(string $accessToken): bool;

    /**
     * @param non-empty-string $accessToken
     */
    public function getPublicOfficialPersonStatus(string $accessToken): bool;
}
