<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit\Response;

use function PHPUnit\Framework\assertEquals;

use PHPUnit\Framework\TestCase;
use Vanta\Integration\TId\Response\PairKey;

final class PairKeyTest extends TestCase
{
    public function testCreateSandboxPairKey(): void
    {
        assertEquals(
            new PairKey('TBankSandboxToken', 'Bearer', 10000, 'someIdToken'),
            PairKey::createSandboxPairKey(),
        );
    }
}
