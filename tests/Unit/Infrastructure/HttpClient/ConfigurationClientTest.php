<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit\Infrastructure\HttpClient;

use function PHPUnit\Framework\assertEquals;

use PHPUnit\Framework\TestCase;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;

final class ConfigurationClientTest extends TestCase
{
    public function testConfigurationClient(): void
    {
        $configurationClient = new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru');

        $configurationClient = $configurationClient->withClientId('someOtherClientId');

        assertEquals(
            new ConfigurationClient('someOtherClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            $configurationClient,
        );

        $configurationClient = $configurationClient->withClientSecret('someOtherClientSecret');

        assertEquals(
            new ConfigurationClient('someOtherClientId', 'someOtherClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            $configurationClient,
        );

        $configurationClient = $configurationClient->withIdClientUrl('https://id.tbank2.ru');

        assertEquals(
            new ConfigurationClient('someOtherClientId', 'someOtherClientSecret', 'https://id.tbank2.ru', 'https://business.tbank.ru'),
            $configurationClient,
        );

        $configurationClient = $configurationClient->withBusinessClientUrl('https://business.tbank2.ru');

        assertEquals(
            new ConfigurationClient('someOtherClientId', 'someOtherClientSecret', 'https://id.tbank2.ru', 'https://business.tbank2.ru'),
            $configurationClient,
        );
    }
}
