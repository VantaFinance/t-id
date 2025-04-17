<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Unit\Infrastructure\HttpClient;

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
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

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
