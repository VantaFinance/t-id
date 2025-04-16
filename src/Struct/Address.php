<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Struct;

final readonly class Address
{
    /**
     * @param bool                  $primary    - является основным адресом
     * @param non-empty-string|null $apartment
     * @param non-empty-string|null $building   - строение
     * @param non-empty-string|null $city
     * @param non-empty-string|null $claddrCode
     * @param non-empty-string|null $country
     * @param non-empty-string|null $district   - район
     * @param non-empty-string|null $fiasCode
     * @param non-empty-string|null $house      - номер дома
     * @param non-empty-string|null $housing    - корпус
     * @param non-empty-string|null $region
     * @param non-empty-string|null $settlement - населенный пункт
     * @param non-empty-string|null $street
     * @param non-empty-string|null $zipCode
     */
    public function __construct(
        public AdressType $addressType,
        public bool $primary,
        public ?string $apartment = null,
        public ?string $building = null,
        public ?string $city = null,
        public ?string $claddrCode = null,
        public ?string $country = null,
        public ?string $district = null,
        public ?string $fiasCode = null,
        public ?string $house = null,
        public ?string $housing = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $region = null,
        public ?string $settlement = null,
        public ?string $street = null,
        public ?string $zipCode = null,
    ) {
    }
}
