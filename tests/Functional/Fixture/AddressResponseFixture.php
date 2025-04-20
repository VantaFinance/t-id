<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Functional\Fixture;

use Vanta\Integration\TId\Struct\Address;
use Vanta\Integration\TId\Struct\AddressType;

use function Vanta\Integration\TId\Tests\Unit\fileGetJsonContents;

final readonly class AddressResponseFixture
{
    /**
     * @return array{0: Address, 1: Address}
     */
    public static function getAddressResponseFull(): array
    {
        return [
            new Address(
                AddressType::RESIDENCE_ADDRESS,
                false,
                '100',
                '111',
                'г Ярославль',
                '1200000600010530123',
                'Россия',
                'Ярославский округ',
                '567845f8-72vb-45f3-ad16-bd4d12e06162',
                '120',
                '121',
                57.1234,
                39.5678,
                'Ярославская обл',
                'Ярославль',
                'ул Правды',
                '150001',
            ),
            new Address(
                AddressType::REGISTRATION_ADDRESS,
                false,
                '100',
                '111',
                'г Ярославль',
                '1200000600010530123',
                'Россия',
                'Ярославский округ',
                '567845f8-72vb-45f3-ad16-bd4d12e06162',
                '120',
                '121',
                57.1234,
                39.5678,
                'Ярославская обл',
                'Ярославль',
                'ул Правды',
                '150001',
            ),
        ];
    }

    /**
     * @return non-empty-string
     */
    public static function getAddressResponseFullSerialized(): string
    {
        return fileGetJsonContents(__DIR__ . '/Json/address_response_full.json');
    }

    /**
     * @return array{0: Address}
     */
    public static function getAddressResponseMin(): array
    {
        return [new Address(
            AddressType::REGISTRATION_ADDRESS,
            false,
        )];
    }

    /**
     * @return non-empty-string
     */
    public static function getAddressResponseMinSerialized(): string
    {
        return fileGetJsonContents(__DIR__ . '/Json/address_response_min.json');
    }

    /**
     * @return array{}
     */
    public static function getAddressResponseEmpty(): array
    {
        return [];
    }

    /**
     * @return non-empty-string
     */
    public static function getAddressResponseEmptySerialized(): string
    {
        return fileGetJsonContents(__DIR__ . '/Json/address_response_empty.json');
    }
}
