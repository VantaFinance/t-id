<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit;

use Brick\PhoneNumber\PhoneNumber;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface as Promise;
use GuzzleHttp\Psr7\Response as Psr7Response;
use GuzzleHttp\Psr7\Utils;
use LogicException;
use phpDocumentor\Reflection\Types\Integer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Uid\Uuid;
use Throwable;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\Infrastructure\HttpClient\Exception\BadRequestException;
use Vanta\Integration\TId\Infrastructure\HttpClient\Exception\ForbiddenException;
use Vanta\Integration\TId\Infrastructure\HttpClient\Exception\InternalServerErrorException;
use Vanta\Integration\TId\Infrastructure\HttpClient\Exception\NotFoundException;
use Vanta\Integration\TId\Infrastructure\HttpClient\Exception\UnauthorizedException;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\Middleware;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\SandboxBusinessClientMiddleware;
use Vanta\Integration\TId\Response\User;
use Vanta\Integration\TId\RestClientBuilder;
use Vanta\Integration\TId\Tests\Functional\Fixture\UserResponseFixture;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertContainsOnlyInstancesOf;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

final class RestClientBuilderTest extends TestCase
{
    public function testWithHttpClient(): void
    {
        $builder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(),
        );

        assertNotEquals(spl_object_id($builder), spl_object_id($builder->withClient(new Client())));
    }

    public function testWithSerializer(): void
    {
        $builder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(),
        );

        assertNotEquals(spl_object_id($builder), spl_object_id($builder->withSerializer(new Serializer())));
    }

    public function testConfiguration(): void
    {
        $builder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(),
        );

        assertNotEquals(
            spl_object_id($builder),
            spl_object_id($builder->withConfiguration(new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru')))
        );
    }

    public function testWithMiddlewares(): void
    {
        $builder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(),
        );

        assertNotEquals(spl_object_id($builder), spl_object_id($builder->withMiddlewares([])));
    }

    /**
     * @throws ClientException
     */
    public function testAddMiddleware(): void
    {
        $callCount = 0;

        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use (&$callCount): Psr7Response {
                assertEquals('https://id.tbank.ru/userinfo/userinfo', $request->getUri()->__toString());
                assertEquals('someTestHeaderValue', $request->getHeader('someTestHeaderName')[0]);

                $callCount++;

                return new Psr7Response(status: Status::OK, body: UserResponseFixture::getUserResponseFullSerialized());
            },
            static function (Request $request) use (&$callCount): Psr7Response {
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/pdl/status', $request->getUri()->__toString());
                assertEquals('someTestHeaderValue', $request->getHeader('someTestHeaderName')[0]);

                $callCount++;

                return new Psr7Response(status: Status::OK, body: '{"isPublicOfficialPerson":true}');
            },
        ]);

        $clientBuilder = RestClientBuilder::create(
                new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
                new Client(['handler' => $mock]),
            )
            ->addMiddleware(new class() implements Middleware {
                public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
                {
                    $request = $request->withHeader('someTestHeaderName', 'someTestHeaderValue');

                    return $next($request, $configuration);
                }

            })
        ;

        $clientBuilder
            ->createIdClient()
            ->getUser('someAccessToken')
        ;

        $clientBuilder
            ->createUserStatusClient()
            ->getPublicOfficialPersonStatus('someAccessToken')
        ;

        assertEquals(2, $callCount);
    }

    /**
     * @param positive-int $statusCode
     * @param non-empty-string $expectExceptionClass
     * @throws ClientException
     */
    #[DataProvider('errorMiddlewaresDataProvider')]
    public function testErrorMiddlewares(int $statusCode, string $responseContent, callable $callable, string $expectExceptionClass): void
    {
        $mock = MockHandler::createWithMiddleware([
            static function () use ($statusCode, $responseContent): Psr7Response {
                return new Psr7Response(status: $statusCode, body: $responseContent);
            },
        ]);

        $this->expectException($expectExceptionClass);

        $restClientBuilder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        );

        $callable($restClientBuilder);
    }

    public static function errorMiddlewaresDataProvider(): iterable
    {
            $userStatusClient = static function (RestClientBuilder $restClientBuilder) {
                return $restClientBuilder
                    ->createUserStatusClient()
                    ->getPublicOfficialPersonStatus('someAccessToken')
                ;
            };

        $idClient = static function (RestClientBuilder $restClientBuilder) {
                return $restClientBuilder
                    ->createIdClient()
                    ->getUser('someAccessToken')
                ;
            };

        $documentClient = static function (RestClientBuilder $restClientBuilder) {
                return $restClientBuilder
                    ->createDocumentClient()
                    ->getSnils('someAccessToken')
                ;
            };

        foreach ([$userStatusClient, $idClient, $documentClient] as $callable) {
            yield [Status::UNAUTHORIZED, '', $callable, UnauthorizedException::class];

            yield [Status::FORBIDDEN, '', $callable, ForbiddenException::class];

            yield [Status::NOT_FOUND, '', $callable, NotFoundException::class];

            yield [Status::BAD_REQUEST, '', $callable, BadRequestException::class];

            yield [Status::UNAVAILABLE_FOR_LEGAL_REASONS, '', $callable, BadRequestException::class];

            yield [Status::INTERNAL_SERVER_ERROR, '', $callable, InternalServerErrorException::class];

            yield [Status::NETWORK_AUTHENTICATION_REQUIRED, '', $callable, InternalServerErrorException::class];
        }

        foreach ([$userStatusClient, $documentClient] as $callable) {
            yield [Status::OK, '{}', $callable, NotFoundException::class];
        }
    }

    public function testSandboxBusinessClientMiddleware(): void
    {
        $isRequestWasSent = false;

        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use (&$isRequestWasSent): Psr7Response {
                $isRequestWasSent = true;

                assertEquals('https://business.tbank.ru/openapi/sandbox/api/v1/individual/pdl/status', $request->getUri()->__toString());

                return new Psr7Response(status: Status::OK, body: '{"isPublicOfficialPerson":true}');
            },
        ]);

        $clientBuilder = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->addMiddleware(new SandboxBusinessClientMiddleware())
        ;

        $clientBuilder
            ->createUserStatusClient()
            ->getPublicOfficialPersonStatus('someAccessToken')
        ;

        assertTrue($isRequestWasSent);
    }
}
