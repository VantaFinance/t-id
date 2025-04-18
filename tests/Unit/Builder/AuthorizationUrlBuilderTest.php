<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit\Builder;

use GuzzleHttp\Client;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringEndsWith;
use function PHPUnit\Framework\assertStringStartsWith;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\RestClientBuilder;

final class AuthorizationUrlBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        // без зафиксированного state
        $authUrlBuilder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(),
        )->createAuthUrlBuilder('https://id.tbank.ru', 'https://vanta.ru')
        ;

        assertStringStartsWith('https://id.tbank.ru/auth/authorize?client_id=someClientId&redirect_uri=https%3A%2F%2Fvanta.ru&state=', $authUrlBuilder->build());
        assertStringEndsWith('&response_type=code', $authUrlBuilder->build());

        // с зафиксированным state
        $state = Uuid::fromString('01964397-eb5e-78c4-a551-8b13b1c3d0e9');

        $authUrlBuilder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(),
        )->createAuthUrlBuilder('https://id.tbank.ru', 'https://vanta.ru')
            ->withState($state)
        ;

        $authUrlBuilder = $authUrlBuilder->withBaseUri('https://poscredit.ru');

        assertEquals('https://poscredit.ru/auth/authorize?client_id=someClientId&redirect_uri=https%3A%2F%2Fvanta.ru&state=01964397-eb5e-78c4-a551-8b13b1c3d0e9&response_type=code', $authUrlBuilder->build());

        $authUrlBuilder = $authUrlBuilder->withClientId('someOtherClientId');

        assertEquals('https://poscredit.ru/auth/authorize?client_id=someOtherClientId&redirect_uri=https%3A%2F%2Fvanta.ru&state=01964397-eb5e-78c4-a551-8b13b1c3d0e9&response_type=code', $authUrlBuilder->build());

        $authUrlBuilder = $authUrlBuilder->withRedirectUri('https://poscredit.ru');

        assertEquals('https://poscredit.ru/auth/authorize?client_id=someOtherClientId&redirect_uri=https%3A%2F%2Fposcredit.ru&state=01964397-eb5e-78c4-a551-8b13b1c3d0e9&response_type=code', $authUrlBuilder->build());

        $authUrlBuilder = $authUrlBuilder->withState($state);

        assertEquals('https://poscredit.ru/auth/authorize?client_id=someOtherClientId&redirect_uri=https%3A%2F%2Fposcredit.ru&state=01964397-eb5e-78c4-a551-8b13b1c3d0e9&response_type=code', $authUrlBuilder->build());

        $authUrlBuilder = $authUrlBuilder->withResponseType('someOtherResponseType');

        assertEquals('https://poscredit.ru/auth/authorize?client_id=someOtherClientId&redirect_uri=https%3A%2F%2Fposcredit.ru&state=01964397-eb5e-78c4-a551-8b13b1c3d0e9&response_type=someOtherResponseType', $authUrlBuilder->build());
    }
}
