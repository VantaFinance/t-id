<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Functional\Transport;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as Psr7Response;

use function PHPUnit\Framework\assertEquals;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface as Request;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\Response\AuthIntrospect;
use Vanta\Integration\TId\Response\PairKey;
use Vanta\Integration\TId\Response\User;
use Vanta\Integration\TId\RestClientBuilder;
use Vanta\Integration\TId\Struct\Scope;
use Vanta\Integration\TId\Tests\Functional\Fixture\UserResponseFixture;
use Yiisoft\Http\Method;

final class RestUserClientTest extends TestCase
{
    public function testGetPairKeyByAuthorizationCode(): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request): Psr7Response {
                assertEquals(Method::POST, $request->getMethod());
                assertEquals('https://id.tbank.ru/auth/token', $request->getUri()->__toString());
                assertEquals('grant_type=authorization_code&redirect_uri=https%3A%2F%2Fvanta.ru&code=someCode', $request->getBody()->getContents());
                assertEquals(
                    [
                        'User-Agent'     => ['GuzzleHttp/7'],
                        'Host'           => ['id.tbank.ru'],
                        'Authorization'  => ['Basic c29tZUNsaWVudElkOnNvbWVDbGllbnRTZWNyZXQ='],
                        'Accept'         => ['application/json'],
                        'Content-Length' => [79],
                        'Content-Type'   => ['application/x-www-form-urlencoded'],
                    ],
                    $request->getHeaders()
                );

                return new Psr7Response(body: '{"access_token":"someAccessToken","token_type":"Bearer","expires_in":1800,"id_token":"someIdToken"}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://id.tbank'),
            new Client(['handler' => $mock]),
        )
            ->createUserClient()
            ->getPairKeyByAuthorizationCode('someCode', 'https://vanta.ru')
        ;

        assertEquals(
            new PairKey(
                'someAccessToken',
                'Bearer',
                1800,
                'someIdToken'
            ),
            $responseActual,
        );
    }

    /**
     * @param non-empty-string $responseSerialized
     */
    #[DataProvider('getUserDataProvider')]
    public function testGetUser(User $responseExpected, string $responseSerialized): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use ($responseSerialized): Psr7Response {
                assertEquals(Method::POST, $request->getMethod());
                assertEquals('https://id.tbank.ru/userinfo/userinfo', $request->getUri()->__toString());
                assertEquals('client_id=someClientId&client_secret=someClientSecret', $request->getBody()->getContents());
                assertEquals(
                    [
                        'User-Agent'     => ['GuzzleHttp/7'],
                        'Host'           => ['id.tbank.ru'],
                        'Authorization'  => ['Bearer someAccessToken'],
                        'Accept'         => ['application/json'],
                        'Content-Length' => [53],
                        'Content-Type'   => ['application/x-www-form-urlencoded'],
                    ],
                    $request->getHeaders()
                );

                return new Psr7Response(body: $responseSerialized);
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://id.tbank'),
            new Client(['handler' => $mock]),
        )
            ->createUserClient()
            ->getUser('someAccessToken')
        ;

        assertEquals($responseExpected, $responseActual);
    }

    /**
     * @return iterable<array{0: User, 1: non-empty-string}>
     */
    public static function getUserDataProvider(): iterable
    {
        yield [
            UserResponseFixture::getUserResponseFull(),
            UserResponseFixture::getUserResponseFullSerialized(),
        ];

        yield [
            UserResponseFixture::getUserResponseMin(),
            UserResponseFixture::getUserResponseMinSerialized(),
        ];
    }

    public function testGetAuthIntrospect(): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request): Psr7Response {
                assertEquals(Method::POST, $request->getMethod());
                assertEquals('https://id.tbank.ru/auth/introspect', $request->getUri()->__toString());
                assertEquals('token=someAccessToken', $request->getBody()->getContents());
                assertEquals(
                    [
                        'User-Agent'     => ['GuzzleHttp/7'],
                        'Host'           => ['id.tbank.ru'],
                        'Authorization'  => ['Basic c29tZUNsaWVudElkOnNvbWVDbGllbnRTZWNyZXQ='],
                        'Accept'         => ['application/json'],
                        'Content-Length' => [21],
                        'Content-Type'   => ['application/x-www-form-urlencoded'],
                    ],
                    $request->getHeaders()
                );

                return new Psr7Response(body: '{"active":true,"scope":["PROFILE","PHONE"],"client_id":"someClientId","token_type":"Bearer","exp":1585728196,"iat":1585684996,"sub":"someSub","aud":["some","aud"],"iss":"https://id.tbank.ru/"}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createUserClient()
            ->getAuthIntrospect('someAccessToken')
        ;

        assertEquals(
            new AuthIntrospect(
                true,
                [Scope::PROFILE, Scope::PHONE],
                'someClientId',
                'Bearer',
                new DateTimeImmutable('2020-04-01T08:03:16'),
                new DateTimeImmutable('2020-03-31T20:03:16'),
                'someSub',
                ['some', 'aud'],
                'https://id.tbank.ru/'
            ),
            $responseActual,
        );
    }
}
