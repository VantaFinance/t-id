<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class AuthIntrospect
{
    /**
     * @param list<non-empty-string> $scope
     * @param non-empty-string       $clientId
     * @param non-empty-string       $tokenType
     * @param positive-int           $exp
     * @param positive-int           $iat
     * @param non-empty-string       $sub
     * @param array<string>          $aud
     * @param non-empty-string       $iss
     */
    public function __construct(
        public bool $active,
        public array $scope,
        #[SerializedName('client_id')]
        public string $clientId,
        #[SerializedName('token_type')]
        public string $tokenType,
        public int $exp,
        public int $iat,
        public string $sub,
        public array $aud,
        public string $iss,
    ) {
    }
}
