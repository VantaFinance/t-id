<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Functional\Fixture;

use DateTimeImmutable;
use Vanta\Integration\TId\Response\Document;
use Vanta\Integration\TId\Struct\DocumentType;
use Vanta\Integration\TId\Struct\MaritalStatus;

use function Vanta\Integration\TId\Tests\Unit\fileGetJsonContents;

final readonly class DocumentResponseFixture
{
    public static function getDocumentResponseFull(): Document
    {
        return new Document(
            self::getDocumentResponseFullSerialized(),
            DocumentType::PASSPORT,
            new DateTimeImmutable('06.02.1993'),
            'г. Новосибирск',
            'РФ',
            new DateTimeImmutable('06.02.2000'),
            MaritalStatus::MARRIED,
            new DateTimeImmutable('02.02.2009'),
            0,
            true,
            '123456',
            '7890',
            'Уполномоченный орган',
            new DateTimeImmutable('02.06.2035'),
        );
    }

    /**
     * @return non-empty-string
     */
    public static function getDocumentResponseFullSerialized(): string
    {
        return fileGetJsonContents(__DIR__ . '/Json/document_response_full.json');
    }

    public static function getDocumentResponseMin(): Document
    {
        return new Document(
            self::getDocumentResponseMinSerialized(),
            DocumentType::PASSPORT,
        );
    }

    /**
     * @return non-empty-string
     */
    public static function getDocumentResponseMinSerialized(): string
    {
        return fileGetJsonContents(__DIR__ . '/Json/document_response_min.json');
    }
}
