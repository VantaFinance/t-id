<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit;

use RuntimeException;

/**
 * @param  non-empty-string $path
 * @return non-empty-string
 *
 * @throws RuntimeException
 */
function fileGetJsonContents(string $path): string
{
    $content = file_get_contents($path);

    if (!$content) {
        throw new RuntimeException(sprintf('Error get contents from file: %s', $path));
    }

    $content = json_encode(json_decode($content, true)); // чтобы удалить пробелы форматирования JSON-а

    if (!$content) {
        throw new RuntimeException(sprintf('Error re-encode contents from file: %s', $content));
    }

    return $content;
}
