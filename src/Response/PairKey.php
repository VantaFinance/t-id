<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

final readonly class PairKey
{
    public const string SANDBOX_TOKEN = 'TBankSandboxToken';

    /**
     * @param non-empty-string $accessToken
     * @param non-empty-string $tokenType
     * @param positive-int     $expiresIn
     * @param non-empty-string $idToken
     */
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public string $idToken,
    ) {
    }
}
