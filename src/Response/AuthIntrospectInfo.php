<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class AuthIntrospectInfo
{
    /**
     * @param non-empty-string|null       $active
     * @param list<non-empty-string>|null $scope
     * @param non-empty-string|null       $clientId
     * @param non-empty-string|null       $tokenType
     * @param positive-int|null           $exp
     * @param positive-int|null           $iat
     * @param non-empty-string|null       $sub
     * @param array<string>|null          $aud
     * @param non-empty-string|null       $ibsme
     * @param non-empty-string|null       $iss
     */
    public function __construct(
        public ?string $active = null,
        public ?array $scope = null,
        #[SerializedName('client_id')]
        public ?string $clientId = null,
        #[SerializedName('token_type')]
        public ?string $tokenType = null,
        public ?int $exp = null,
        public ?int $iat = null,
        public ?string $sub = null,
        public ?array $aud = null,
        public ?string $ibsme = null,
        public ?string $iss = null,
    ) {
    }
}
