<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use Brick\PhoneNumber\PhoneNumber;
use DateTimeImmutable;
use Vanta\Integration\TId\Struct\Gender;

// @todo однообразный нейминг ответов: info/не info

final readonly class UserInfo
{
    /**
     * @param non-empty-string      $rawValue
     * @param non-empty-string      $sub        - идентификатор авторизированного пользователя
     * @param non-empty-string|null $name       - в доке т банка сказано: фамилия, имя
     * @param non-empty-string|null $familyName - фамилия
     * @param non-empty-string|null $givenName  - имя
     * @param non-empty-string|null $middleName - отчество
     */
    public function __construct(
        public string $rawValue,
        public string $sub,
        public ?string $name = null, // @todo переименовать на firstNameAndName? Или какие термины чаще встречаются в системе?
        public ?Gender $gender = null,
        public ?DateTimeImmutable $birthdate = null,
        public ?string $familyName = null,
        public ?string $givenName = null,
        public ?string $middleName = null,
        public ?PhoneNumber $phoneNumber = null,
    ) {
    }
}
