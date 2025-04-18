<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

enum Scope: string
{
    // По информации от коллег из т-банка,
    // после успешно пройденной авторизации в scopes будет обязательно:
    case PROFILE                                      = 'PROFILE';
    case PHONE                                        = 'PHONE';
    case OPENSME_INDIVIDUAL_IDENTIFICATION_STATUS_GET = 'OPENSME/INDIVIDUAL/IDENTIFICATION/STATUS/GET';
    case OPENSME_INDIVIDUAL_PASSPORT_CHECK_SMEV4      = 'OPENSME/INDIVIDUAL/PASSPORT-CHECK-SMEV4';
    case OPENSME_INDIVIDUAL_PASSPORT_GET              = 'OPENSME/INDIVIDUAL/PASSPORT/GET';
    case OPENSME_INDIVIDUAL_ADDRESSES_GET             = 'OPENSME/INDIVIDUAL/ADDRESSES/GET';
    case OPENSME_INDIVIDUAL_SNILS_GET                 = 'OPENSME/INDIVIDUAL/SNILS/GET';
    case OPENSME_INDIVIDUAL_INN_GET                   = 'OPENSME/INDIVIDUAL/INN/GET';

    // опционально (пользователь может отказаться от передачи):
    case OPENSME_INDIVIDUAL_PDL_STATUS_GET          = 'OPENSME/INDIVIDUAL/PDL/STATUS/GET';
    case OPENSME_INDIVIDUAL_FOREIGNAGENT_STATUS_GET = 'OPENSME/INDIVIDUAL/FOREIGNAGENT/STATUS/GET';
    case OPENSME_INDIVIDUAL_BLACKLIST_STATUS_GET    = 'OPENSME/INDIVIDUAL/BLACKLIST/STATUS/GET';
}
