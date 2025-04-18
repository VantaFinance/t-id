<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Functional\Transport;

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
use Vanta\Integration\TId\Tests\Functional\Fixture\UserResponseFixture;
use Yiisoft\Http\Method;

final class RestIdClientTest extends TestCase
{
    public function testGetPairKeyByAuthorizationCode(): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function (Request $request): Psr7Response {
                assertEquals(Method::POST, $request->getMethod());
                assertEquals('https://id.tbank.ru/auth/token', $request->getUri()->__toString());
                assertEquals('grant_type=authorization_code&redirect_uri=https%3A%2F%2Fvanta.ru&code=someCode', $request->getBody()->getContents());
                assertEquals('Basic c29tZUNsaWVudElkOnNvbWVDbGllbnRTZWNyZXQ=', $request->getHeader('Authorization')[0]);

                return new Psr7Response(body: '{"access_token":"someAccessToken","token_type":"Bearer","expires_in":1800,"id_token":"someIdToken"}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createIdClient()
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
                assertEquals('Bearer someAccessToken', $request->getHeader('Authorization')[0]);

                return new Psr7Response(body: $responseSerialized);
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createIdClient()
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
                assertEquals('Basic c29tZUNsaWVudElkOnNvbWVDbGllbnRTZWNyZXQ=', $request->getHeader('Authorization')[0]);

                return new Psr7Response(body: '{"active":true,"scope":["some","scopes"],"client_id":"someClientId","token_type":"Bearer","exp":1585728196,"iat":1585684996,"sub":"someSub","aud":["some","aud"],"iss":"https://id.tbank.ru/"}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createIdClient()
            ->getAuthIntrospect('someAccessToken')
        ;

        assertEquals(
            new AuthIntrospect(
                true,
                ['some', 'scopes'],
                'someClientId',
                'Bearer',
                1585728196,
                1585684996,
                'someSub',
                ['some', 'aud'],
                'https://id.tbank.ru/'
            ),
            $responseActual,
        );
    }
}
