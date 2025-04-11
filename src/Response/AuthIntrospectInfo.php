<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

final readonly class AuthIntrospectInfo
{
    /**
     * @todo выяснить набор scope-ов
     * @todo выяснить формат полей, обязательность, nullable
     */
    public function __construct(
        public ?string $active = null,
        public ?array $scope = null,
        public ?string $client_id = null,
        public ?string $token_type = null,
        public ?int $exp = null,
        public ?int $iat = null,
        public ?string $sub = null,
        public ?array $aud = null,
        public ?string $ibsme = null,
        public ?string $iss = null,
    ) {
    }
}
