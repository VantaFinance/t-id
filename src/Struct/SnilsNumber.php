<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

use Webmozart\Assert\Assert;

final readonly class SnilsNumber
{
    /**
     * @param numeric-string $number
     */
    public function __construct(
        public string $number,
    ) {
        Assert::regex($number, '/^\d{11}$/', 'Invalid snils number, expecting 11 digits');
    }
}
