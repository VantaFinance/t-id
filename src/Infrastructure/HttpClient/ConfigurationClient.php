<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient;

final readonly class ConfigurationClient
{
    /**
     * @param non-empty-string $clientId
     * @param non-empty-string $clientSecret
     * @param non-empty-string $url
     */
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $url,
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
            url: $this->url,
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
            url: $this->url,
        );
    }

    /**
     * @param non-empty-string $url
     */
    public function withUrl(string $url): self
    {
        return new self(
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            url: $url,
        );
    }
}
