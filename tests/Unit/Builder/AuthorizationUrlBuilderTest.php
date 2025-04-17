<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit\Builder;

use Brick\PhoneNumber\PhoneNumber;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface as Promise;
use GuzzleHttp\Psr7\Response as Psr7Response;
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
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\SandboxBusinessClientMiddleware;
use Vanta\Integration\TId\RestClientBuilder;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertContainsOnlyInstancesOf;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertStringEndsWith;
use function PHPUnit\Framework\assertStringStartsWith;
use function PHPUnit\Framework\assertTrue;

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
