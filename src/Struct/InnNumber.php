<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

use Webmozart\Assert\Assert;

final readonly class InnNumber
{
    /**
     * @param numeric-string $number
     */
    public function __construct(
        public string $number,
    ) {
        Assert::regex($number, '/^\d{10}(\d{2})?$/', 'Invalid inn number, expecting 10 or 12 digits');
    }
}
