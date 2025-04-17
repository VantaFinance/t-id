<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit\Infrastructure\Serializer;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Exception\ExceptionInterface as Exception;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

use Vanta\Integration\TId\Infrastructure\Serializer\Normalizer\PhoneNumberNormalizer;
use function PHPUnit\Framework\assertEquals;

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
    public function testFailDenormalize(): void
    {
        $this->expectExceptionObject(new UnexpectedValueException('The string supplied did not seem to be a phone number.'));

        $normalizer = new PhoneNumberNormalizer();

        $normalizer->denormalize('pos', PhoneNumber::class);
    }
}
