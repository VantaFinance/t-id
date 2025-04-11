<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

enum DocumentType: string
{
    case PASSPORT                       = 'PASSPORT';
    case FOREIGN_PASSPORT               = 'FOREIGN_PASSPORT';
    case FOREIGN_INTERNATIONAL_PASSPORT = 'FOREIGN_INTERNATIONAL_PASSPORT';
    case RF_INTERNATIONAL_PASSPORT      = 'RF_INTERNATIONAL_PASSPORT';
    case BIRTH_CERTIFICATE              = 'BIRTH_CERTIFICATE';
    case RF_RESIDENCE_PERMIT            = 'RF_RESIDENCE_PERMIT';
}
