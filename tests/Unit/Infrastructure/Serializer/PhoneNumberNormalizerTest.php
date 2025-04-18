<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit\Infrastructure\Serializer;

use Brick\PhoneNumber\PhoneNumber;
use DateTimeImmutable;

use function PHPUnit\Framework\assertEquals;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface as Exception;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Vanta\Integration\TId\Infrastructure\Serializer\Normalizer\PhoneNumberNormalizer;

final class PhoneNumberNormalizerTest extends TestCase
{
    /**
     * @param scalar $value
     */
    #[DataProvider('supportsDenormalizationDataProvider')]
    public function testSupportsDenormalization(bool $result, ?string $type, $value): void
    {
        $normalizer = new PhoneNumberNormalizer();

        assertEquals($result, $normalizer->supportsDenormalization($value, $type));
    }

    /**
     * @return iterable<array{0: bool, 1: ?string, 2: scalar }>
     */
    public static function supportsDenormalizationDataProvider(): iterable
    {
        yield [true, PhoneNumber::class, '+79994652397'];
        yield [false, '', false];
        yield [false, null, ''];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('failDenormalizeDataProvider')]
    public function testFailDenormalize(mixed $phoneNumber, \Exception $expectedException): void
    {
        $this->expectExceptionObject($expectedException);

        $normalizer = new PhoneNumberNormalizer();

        $normalizer->denormalize($phoneNumber, PhoneNumber::class);
    }

    /**
     * @return iterable<array{0: bool, 1: ?string, 2: scalar }>
     */
    public static function failDenormalizeDataProvider(): iterable
    {
        yield ['pos', new UnexpectedValueException('The string supplied did not seem to be a phone number')];
        yield [123, new UnexpectedValueException('Expected a string. Got: integer')];
        yield [1.23, new UnexpectedValueException('Expected a string. Got: double')];
        yield [false, new UnexpectedValueException('Expected a string. Got: boolean')];
        yield [new DateTimeImmutable(), new UnexpectedValueException('Expected a string. Got: DateTimeImmutable')];
    }
}
