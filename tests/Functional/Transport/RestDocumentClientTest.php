<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Tests\Functional\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as Psr7Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface as Request;
use Symfony\Component\Uid\Uuid;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\Response\Document;
use Vanta\Integration\TId\Response\InnNumber;
use Vanta\Integration\TId\Response\SnilsNumber;
use Vanta\Integration\TId\RestClientBuilder;
use Vanta\Integration\TId\Struct\Address;
use Vanta\Integration\TId\Struct\AddressType;
use Vanta\Integration\TId\Struct\DocumentType;
use Vanta\Integration\TId\Tests\Functional\Fixture\AddressResponseFixture;
use Vanta\Integration\TId\Tests\Functional\Fixture\DocumentResponseFixture;
use Yiisoft\Http\Method;

use function PHPUnit\Framework\assertEquals;

final class RestDocumentClientTest extends TestCase
{
    /**
     * @param non-empty-string $responseSerialized
     */
    #[DataProvider('getDocumentDataProvider')]
    public function testGetDocument(Document $responseExpected, string $responseSerialized): void
    {
        $xRequestId = Uuid::v7();

        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use ($responseSerialized, $xRequestId): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/documents/passport?idType=PASSPORT', $request->getUri()->__toString());
                assertEquals('Bearer someAccessToken', $request->getHeader('Authorization')[0]);
                assertEquals($xRequestId->jsonSerialize(), $request->getHeader('X-Request-Id')[0]);

                return new Psr7Response(body: $responseSerialized);
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createDocumentClient()
            ->getDocument('someAccessToken', DocumentType::PASSPORT, $xRequestId)
        ;

        assertEquals($responseExpected, $responseActual);
    }

    /**
     * @return iterable<array{0: Document, 1: non-empty-string}>
     */
    public static function getDocumentDataProvider(): iterable
    {
        yield [
            DocumentResponseFixture::getDocumentResponseFull(),
            DocumentResponseFixture::getDocumentResponseFullSerialized(),
        ];

        yield [
            DocumentResponseFixture::getDocumentResponseMin(),
            DocumentResponseFixture::getDocumentResponseMinSerialized(),
        ];
    }

    /**
     * @param list<Address> $responseExpected
     * @param non-empty-string $responseSerialized
     */
    #[DataProvider('getAddressDataProvider')]
    public function testGetAddress(array $responseExpected, string $responseSerialized): void
    {
        $xRequestId = Uuid::v7();

        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use ($responseSerialized, $xRequestId): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/addresses?addressType=REGISTRATION_ADDRESS', $request->getUri()->__toString());
                assertEquals('Bearer someAccessToken', $request->getHeader('Authorization')[0]);
                assertEquals($xRequestId->jsonSerialize(), $request->getHeader('X-Request-Id')[0]);

                return new Psr7Response(body: $responseSerialized);
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createDocumentClient()
            ->getAddress('someAccessToken', AddressType::REGISTRATION_ADDRESS, $xRequestId)
        ;

        assertEquals($responseExpected, $responseActual);
    }

    /**
     * @return iterable<array{0: list<Address>, 1: non-empty-string}>
     */
    public static function getAddressDataProvider(): iterable
    {
        yield [
            AddressResponseFixture::getAddressResponseFull(),
            AddressResponseFixture::getAddressResponseFullSerialized(),
        ];

        yield [
            AddressResponseFixture::getAddressResponseMin(),
            AddressResponseFixture::getAddressResponseMinSerialized(),
        ];

        yield [
            AddressResponseFixture::getAddressResponseEmpty(),
            AddressResponseFixture::getAddressResponseEmptySerialized(),
        ];
    }

    /**
     * @param non-empty-string $responseSerialized
     */
    #[DataProvider('getPassportCheckSmevResultProvider')]
    public function testGetPassportCheckSmevResult(bool $responseExpected, string $responseSerialized): void
    {
        $xRequestId = Uuid::v7();

        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use ($responseSerialized, $xRequestId): Psr7Response {
                assertEquals(Method::POST, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/documents/passport-check-smev4', $request->getUri()->__toString());
                assertEquals('Bearer someAccessToken', $request->getHeader('Authorization')[0]);
                assertEquals($xRequestId->jsonSerialize(), $request->getHeader('X-Request-Id')[0]);

                return new Psr7Response(body: $responseSerialized);
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createDocumentClient()
            ->getPassportCheckSmevResult('someAccessToken', $xRequestId)
        ;

        assertEquals($responseExpected, $responseActual);
    }

    /**
     * @return iterable<array{0: Address, 1: non-empty-string}>
     */
    public static function getPassportCheckSmevResultProvider(): iterable
    {
        yield [
            true,
            '{"result": "VALID"}',
        ];

        yield [
            false,
            '{"result": "INVALID"}',
        ];
    }

    public function testGetInn(): void
    {
        $xRequestId = Uuid::v7();

        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use ($xRequestId): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/documents/inn', $request->getUri()->__toString());
                assertEquals('Bearer someAccessToken', $request->getHeader('Authorization')[0]);
                assertEquals($xRequestId->jsonSerialize(), $request->getHeader('X-Request-Id')[0]);

                return new Psr7Response(body: '{"inn":"123456789012"}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createDocumentClient()
            ->getInn('someAccessToken', $xRequestId)
        ;

        assertEquals(new InnNumber('123456789012'), $responseActual);
    }

    public function testGetSnils(): void
    {
        $xRequestId = Uuid::v7();

        $mock = MockHandler::createWithMiddleware([
            static function (Request $request) use ($xRequestId): Psr7Response {
                assertEquals(Method::GET, $request->getMethod());
                assertEquals('https://business.tbank.ru/openapi/api/v1/individual/documents/snils', $request->getUri()->__toString());
                assertEquals('Bearer someAccessToken', $request->getHeader('Authorization')[0]);
                assertEquals($xRequestId->jsonSerialize(), $request->getHeader('X-Request-Id')[0]);

                return new Psr7Response(body: '{"snils":"12345678901"}');
            },
        ]);

        $responseActual = RestClientBuilder::create(
            new ConfigurationClient('someClientId', 'someClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'),
            new Client(['handler' => $mock]),
        )
            ->createDocumentClient()
            ->getSnils('someAccessToken', $xRequestId)
        ;

        assertEquals(new SnilsNumber('12345678901'), $responseActual);
    }
}
