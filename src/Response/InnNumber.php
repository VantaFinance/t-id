<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use Webmozart\Assert\Assert;

final readonly class InnNumber
{
    /**
     * @param numeric-string $inn
     */
    public function __construct(
        public string $inn,
    ) {
        Assert::regex($inn, '/^\d{10}(\d{2})?$/', 'Invalid inn number, expecting 10 or 12 digits');
    }
}
