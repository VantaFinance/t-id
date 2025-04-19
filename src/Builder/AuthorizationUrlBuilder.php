<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Builder;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

final readonly class AuthorizationUrlBuilder
{
    /**
     * @param non-empty-string $baseUri
     * @param non-empty-string $clientId
     * @param non-empty-string $redirectUri
     * @param non-empty-string $responseType
     */
    public function __construct(
        private string $baseUri,
        private string $clientId,
        private string $redirectUri,
        private Uuid $state = new UuidV7(),
        private string $responseType = 'code',
    ) {
    }

    /**
     * @param non-empty-string $baseUri
     */
    public function withBaseUri(string $baseUri): self
    {
        return new self(
            baseUri: $baseUri,
            clientId: $this->clientId,
            redirectUri: $this->redirectUri,
            state: $this->state,
            responseType: $this->responseType,
        );
    }

    /**
     * @param non-empty-string $clientId
     */
    public function withClientId(string $clientId): self
    {
        return new self(
            baseUri: $this->baseUri,
            clientId: $clientId,
            redirectUri: $this->redirectUri,
            state: $this->state,
            responseType: $this->responseType,
        );
    }

    /**
     * @param non-empty-string $redirectUri
     */
    public function withRedirectUri(string $redirectUri): self
    {
        return new self(
            baseUri: $this->baseUri,
            clientId: $this->clientId,
            redirectUri: $redirectUri,
            state: $this->state,
            responseType: $this->responseType,
        );
    }

    public function withState(Uuid $state): self
    {
        return new self(
            baseUri: $this->baseUri,
            clientId: $this->clientId,
            redirectUri: $this->redirectUri,
            state: $state,
            responseType: $this->responseType,
        );
    }

    /**
     * @param non-empty-string $responseType
     */
    public function withResponseType(string $responseType): self
    {
        return new self(
            baseUri: $this->baseUri,
            clientId: $this->clientId,
            redirectUri: $this->redirectUri,
            state: $this->state,
            responseType: $responseType,
        );
    }

    /**
     * @return non-empty-string
     */
    public function build(): string
    {
        return sprintf(
            '%s/auth/authorize?%s',
            $this->baseUri,
            http_build_query([
                'client_id'     => $this->clientId,
                'redirect_uri'  => $this->redirectUri,
                'state'         => $this->state->jsonSerialize(),
                'response_type' => $this->responseType,
            ])
        );
    }
}
