<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient;

final readonly class ConfigurationClient
{
    /**
     * @param non-empty-string $clientId
     * @param non-empty-string $clientSecret
     * @param non-empty-string $idApiUrl
     * @param non-empty-string $businessApiUrl
     */
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $idApiUrl,
        public string $businessApiUrl,
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
            idApiUrl: $this->idApiUrl,
            businessApiUrl: $this->businessApiUrl,
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
            idApiUrl: $this->idApiUrl,
            businessApiUrl: $this->businessApiUrl,
        );
    }

    /**
     * @param non-empty-string $idApiUrl
     */
    public function withIdApiUrl(string $idApiUrl): self
    {
        return new self(
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            idApiUrl: $idApiUrl,
            businessApiUrl: $this->businessApiUrl,
        );
    }

    /**
     * @param non-empty-string $businessApiUrl
     */
    public function withBusinessApiUrl(string $businessApiUrl): self
    {
        return new self(
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            idApiUrl: $this->idApiUrl,
            businessApiUrl: $businessApiUrl,
        );
    }
}
