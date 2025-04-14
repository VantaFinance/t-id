<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient;

final readonly class ConfigurationClient
{
    /**
     * @param non-empty-string $clientId
     * @param non-empty-string $clientSecret
     * @param non-empty-string $idClientUrl
     * @param non-empty-string $businessClientUrl
     */
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $idClientUrl,
        public string $businessClientUrl,
    ) {
    }

    /**
     * @param non-empty-string $clientId
     */
    public function withClientId(string $clientId): self
    {
        return new self(
            clientId: $clientId,
            clientSecret: $this->clientSecret,
            idClientUrl: $this->idClientUrl,
            businessClientUrl: $this->businessClientUrl,
        );
    }

    /**
     * @param non-empty-string $clientSecret
     */
    public function withClientSecret(string $clientSecret): self
    {
        return new self(
            clientId: $this->clientId,
            clientSecret: $clientSecret,
            idClientUrl: $this->idClientUrl,
            businessClientUrl: $this->businessClientUrl,
        );
    }

    /**
     * @param non-empty-string $idClientUrl
     */
    public function withIdClientUrl(string $idClientUrl): self
    {
        return new self(
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            idClientUrl: $idClientUrl,
            businessClientUrl: $this->businessClientUrl,
        );
    }

    /**
     * @param non-empty-string $businessClientUrl
     */
    public function withBusinessClientUrl(string $businessClientUrl): self
    {
        return new self(
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            idClientUrl: $this->idClientUrl,
            businessClientUrl: $businessClientUrl,
        );
    }
}
