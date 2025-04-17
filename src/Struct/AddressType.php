<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

enum AddressType: string
{
    /**
     * Адрес регистрации
     */
    case RESIDENCE_ADDRESS = 'RESIDENCE_ADDRESS';

    /**
     * Рабочий адрес
     */
    case REGISTRATION_ADDRESS = 'REGISTRATION_ADDRESS';

    /**
     * Домашний адрес
     */
    case WORK_ADDRESS = 'WORK_ADDRESS';

    /**
     * Адрес доставки
     */
    case DELIVERY_ADDRESS = 'DELIVERY_ADDRESS';
}
