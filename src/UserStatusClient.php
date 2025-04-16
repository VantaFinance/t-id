<?php

declare(strict_types=1);

namespace Vanta\Integration\TId;

use Psr\Http\Client\ClientExceptionInterface as ClientException;

interface UserStatusClient
{
    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getForeignAgentStatus(string $accessToken): bool;

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
