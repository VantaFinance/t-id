<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use Webmozart\Assert\Assert;

final readonly class SnilsNumber
{
    /**
     * @param numeric-string $snils
     */
    public function __construct(
        public string $snils,
    ) {
        Assert::regex($snils, '/^\d{11}$/', 'Invalid snils number, expecting 11 digits');
    }
}
