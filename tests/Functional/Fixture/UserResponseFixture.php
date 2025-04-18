<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Functional\Fixture;

use Brick\PhoneNumber\PhoneNumber;
use DateTimeImmutable;
use Vanta\Integration\TId\Response\User;
use Vanta\Integration\TId\Struct\Gender;

use function Vanta\Integration\TId\Tests\Unit\fileGetJsonContents;

final readonly class UserResponseFixture
{
    public static function getUserResponseFull(): User
    {
        return new User(
            self::getUserResponseFullSerialized(),
            'someSub',
            'Иванов Василий',
            Gender::MALE,
            new DateTimeImmutable('02.11.1992'),
            'Иванов',
            'Василий',
            'Петрович',
            PhoneNumber::parse('+79612272828'),
        );
    }

    /**
     * @return non-empty-string
     */
    public static function getUserResponseFullSerialized(): string
    {
        return fileGetJsonContents(__DIR__ . '/Json/user_response_full.json');
    }

    public static function getUserResponseMin(): User
    {
        return new User(
            self::getUserResponseMinSerialized(),
            'someSub',
        );
    }

    /**
     * @return non-empty-string
     */
    public static function getUserResponseMinSerialized(): string
    {
        return fileGetJsonContents(__DIR__ . '/Json/user_response_min.json');
    }
}
