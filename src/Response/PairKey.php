<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use Symfony\Component\Serializer\Attribute\SerializedName;

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
        #[SerializedName('access_token')]
        public string $accessToken,
        #[SerializedName('token_type')]
        public string $tokenType,
        #[SerializedName('expires_in')]
        public int $expiresIn,
        #[SerializedName('id_token')]
        public string $idToken,
    ) {
    }
}
