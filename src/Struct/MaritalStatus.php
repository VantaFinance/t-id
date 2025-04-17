<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

enum MaritalStatus: string
{
    case CIVIL_MARRIAGE = 'CIVIL_MARRIAGE';
    case DIVORCED = 'DIVORCED';
    case LIVE_SEPARATELY = 'LIVE_SEPARATELY';
    case MARRIED = 'MARRIED';
    case SINGLE = 'SINGLE';
    case WIDOWED = 'WIDOWED';
}
