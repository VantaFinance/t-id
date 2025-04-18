<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Vanta\Integration\TId\Struct\Scope;

final readonly class AuthIntrospect
{
    /**
     * @param non-empty-list<Scope> $scope
     * @param non-empty-string      $clientId
     * @param non-empty-string      $tokenType
     * @param non-empty-string      $sub
     * @param array<string>         $aud
     * @param non-empty-string      $iss
     */
    public function __construct(
        public bool $active,
        public array $scope,
        #[SerializedName('client_id')]
        public string $clientId,
        #[SerializedName('token_type')]
        public string $tokenType,
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'U'])]
        public DateTimeImmutable $exp,
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'U'])]
        public DateTimeImmutable $iat,
        public string $sub,
        public array $aud,
        public string $iss,
    ) {
    }
}
