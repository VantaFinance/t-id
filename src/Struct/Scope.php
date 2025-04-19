<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

enum Scope: string
{
    // По информации от коллег из т-банка,
    // после успешно пройденной авторизации в scopes будет обязательно:
    case PROFILE                                      = 'profile';
    case PHONE                                        = 'phone';
    case OPENSME_INDIVIDUAL_IDENTIFICATION_STATUS_GET = 'opensme/individual/identification/status/get';
    case OPENSME_INDIVIDUAL_PASSPORT_CHECK_SMEV4      = 'opensme/individual/passport-check-smev4';
    case OPENSME_INDIVIDUAL_PASSPORT_GET              = 'opensme/individual/passport/get';
    case OPENSME_INDIVIDUAL_ADDRESSES_GET             = 'opensme/individual/addresses/get';
    case OPENSME_INDIVIDUAL_SNILS_GET                 = 'opensme/individual/snils/get';
    case OPENSME_INDIVIDUAL_INN_GET                   = 'opensme/individual/inn/get';

    // опционально (пользователь может отказаться от передачи):
    case OPENSME_INDIVIDUAL_PDL_STATUS_GET          = 'opensme/individual/pdl/status/get';
    case OPENSME_INDIVIDUAL_FOREIGNAGENT_STATUS_GET = 'opensme/individual/foreignagent/status/get';
    case OPENSME_INDIVIDUAL_BLACKLIST_STATUS_GET    = 'opensme/individual/blacklist/status/get';

    // @todo узнать
    case ORIGIN            = 'origin';
    case IRIS_RISK_SESSION = 'iris_risk_session';
    case OPENID            = 'openid';
}
