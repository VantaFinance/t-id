<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Response;

use DateTimeImmutable;
use Vanta\Integration\TId\Struct\DocumentType;

final readonly class Document
{
    /**
     * @param non-empty-string      $rawValue
     * @param non-empty-string|null $birthPlace
     * @param non-empty-string|null $citizenship
     * @param non-empty-string|null $maritalStatus
     * @param non-negative-int|null $numberOfChildren
     * @param non-empty-string|null $serialNumber
     * @param non-empty-string|null $unitCode
     * @param non-empty-string|null $unitName
     */
    public function __construct(
        public string $rawValue,
        public DocumentType $idType,
        public ?DateTimeImmutable $birthDate = null,
        public ?string $birthPlace = null,
        public ?string $citizenship = null,
        public ?DateTimeImmutable $issueDate = null,
        public ?string $maritalStatus = null,
        public ?DateTimeImmutable $marriageDate = null,
        public ?int $numberOfChildren = null,
        public ?bool $resident = null,
        public ?string $serialNumber = null,
        public ?string $unitCode = null,
        public ?string $unitName = null,
        public ?DateTimeImmutable $validTo = null,
    ) {
    }
}
