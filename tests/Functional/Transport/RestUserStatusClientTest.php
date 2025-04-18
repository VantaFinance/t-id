<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Functional\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as Psr7Response;

use function PHPUnit\Framework\assertEquals;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface as Request;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\RestClientBuilder;
use Yiisoft\Http\Method;

final class RestUserStatusClientTest extends TestCase
{
    public function testGetForeignAgentStatus(): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/foreignagent/status', $request->getUri()->__toString());
                assertEquals(
                    [
                        'User-Agent'    => ['GuzzleHttp/7'],
                        'Host'          => ['business.tbank.ru'],
                        'Authorization' => ['Bearer someAccessToken'],
                        'Accept'        => ['application/json'],
                    ],
                    $request->getHeaders()
                );

                return new Psr7Response(body: '{"isForeignAgent":true}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createUserStatusClient()
            ->getForeignAgentStatus('someAccessToken')
        ;

        assertEquals(true, $responseActual);
    }

    public function testGetBanksBlackListStatus(): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/blacklist/status', $request->getUri()->__toString());
                assertEquals(
                    [
                        'User-Agent'    => ['GuzzleHttp/7'],
                        'Host'          => ['business.tbank.ru'],
                        'Authorization' => ['Bearer someAccessToken'],
                        'Accept'        => ['application/json'],
                    ],
                    $request->getHeaders()
                );

                return new Psr7Response(body: '{"isBlacklisted":true}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createUserStatusClient()
            ->getBanksBlackListStatus('someAccessToken')
        ;

        assertEquals(true, $responseActual);
    }

    public function testGetIdentificationStatus(): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/identification/status', $request->getUri()->__toString());
                assertEquals(
                    [
                        'User-Agent'    => ['GuzzleHttp/7'],
                        'Host'          => ['business.tbank.ru'],
                        'Authorization' => ['Bearer someAccessToken'],
                        'Accept'        => ['application/json'],
                    ],
                    $request->getHeaders()
                );

                return new Psr7Response(body: '{"isIdentified":true}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createUserStatusClient()
            ->getIdentificationStatus('someAccessToken')
        ;

        assertEquals(true, $responseActual);
    }

    public function testGetPdlStatus(): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/pdl/status', $request->getUri()->__toString());
                assertEquals(
                    [
                        'User-Agent'    => ['GuzzleHttp/7'],
                        'Host'          => ['business.tbank.ru'],
                        'Authorization' => ['Bearer someAccessToken'],
                        'Accept'        => ['application/json'],
                    ],
                    $request->getHeaders()
                );

                return new Psr7Response(body: '{"isPublicOfficialPerson":true}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createUserStatusClient()
            ->getPublicOfficialPersonStatus('someAccessToken')
        ;

        assertEquals(true, $responseActual);
    }
}
