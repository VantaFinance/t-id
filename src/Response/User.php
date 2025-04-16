<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use Brick\PhoneNumber\PhoneNumber;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Vanta\Integration\TId\Struct\Gender;

final readonly class User
{
    /**
     * @param non-empty-string      $rawValue
     * @param non-empty-string      $sub                    - идентификатор авторизированного пользователя
     * @param non-empty-string|null $familyNameAndGivenName - в доке т банка сказано: фамилия, имя
     * @param non-empty-string|null $familyName             - фамилия
     * @param non-empty-string|null $givenName              - имя
     * @param non-empty-string|null $middleName             - отчество
     */
    public function __construct(
        public string $rawValue,
        public string $sub,
        #[SerializedName('name')]
        public ?string $familyNameAndGivenName = null,
        public ?Gender $gender = null,
        public ?DateTimeImmutable $birthdate = null,
        #[SerializedName('family_name')]
        public ?string $familyName = null,
        #[SerializedName('given_name')]
        public ?string $givenName = null,
        #[SerializedName('middle_name')]
        public ?string $middleName = null,
        #[SerializedName('phone_number')]
        public ?PhoneNumber $phoneNumber = null,
    ) {
    }
}
