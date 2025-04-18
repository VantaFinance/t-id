<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\Serializer\Normalizer;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberException;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface as Denormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as Normalizer;
use Webmozart\Assert\Assert;

final readonly class PhoneNumberNormalizer implements Denormalizer
{
    /**
     * @param non-empty-string|null $format
     *
     * @return non-empty-array<class-string<PhoneNumber>, true>
     */
    public function getSupportedTypes(?string $format): array
    {
        /* @infection-ignore-all */
        return [PhoneNumber::class => true];
    }

    /**
     * @param class-string<PhoneNumber>                      $type
     * @param non-empty-string|null                          $format
     * @param array{deserialization_path?: non-empty-string} $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): PhoneNumber
    {
        try {
            return PhoneNumber::parse($data, 'RU');
        } catch (PhoneNumberException $e) {
            /* @infection-ignore-all */
            throw NotNormalizableValueException::createForUnexpectedDataType(
                $e->getMessage(),
                $data,
                [Type::BUILTIN_TYPE_STRING],
                $context['deserialization_path'] ?? null,
                true
            );
        }
    }

    /**
     * @param non-empty-string|null $type
     * @param non-empty-string|null $format
     * @param array<string, mixed>  $context
     */
    public function supportsDenormalization(mixed $data, ?string $type = null, ?string $format = null, array $context = []): bool
    {
        return PhoneNumber::class === $type;
    }
}
