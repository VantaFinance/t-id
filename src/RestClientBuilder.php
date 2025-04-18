<?php

declare(strict_types=1);

namespace Vanta\Integration\TId;

use Psr\Http\Client\ClientInterface as PsrHttpClient;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder as JsonEncoderSymfony;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\TId\Builder\AuthorizationUrlBuilder;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\Infrastructure\HttpClient\HttpClient;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\ClientErrorMiddleware;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\InternalServerMiddleware;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\Middleware;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\PipelineMiddleware;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\UrlMiddleware;
use Vanta\Integration\TId\Infrastructure\Serializer\Normalizer\PhoneNumberNormalizer;
use Vanta\Integration\TId\Transport\RestDocumentClient;
use Vanta\Integration\TId\Transport\RestUserClient;
use Vanta\Integration\TId\Transport\RestUserStatusClient;

final readonly class RestClientBuilder
{
    /**
     * @param non-empty-list<Middleware> $middlewaresIdApi
     * @param non-empty-list<Middleware> $middlewaresBusinessApi
     */
    private function __construct(
        private PsrHttpClient $client,
        public Serializer $serializer,
        private ConfigurationClient $configuration,
        private array $middlewaresIdApi,
        private array $middlewaresBusinessApi,
    ) {
    }

    public static function create(ConfigurationClient $configuration, PsrHttpClient $client): self
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $objectNormalizer     = new ObjectNormalizer(
            $classMetadataFactory,
            new MetadataAwareNameConverter($classMetadataFactory),
            null,
            new PropertyInfoExtractor(),
            new ClassDiscriminatorFromClassMetadata($classMetadataFactory),
        );

        $normalizers = [
            new BackedEnumNormalizer(),
            new UidNormalizer(),
            new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => '!Y-m-d+']),
            new PhoneNumberNormalizer(),
            new UnwrappingDenormalizer(),
            $objectNormalizer,
            new ArrayDenormalizer(),
        ];

        $middlewaresIdApi = [
            new ClientErrorMiddleware(),
            new InternalServerMiddleware(),
            new UrlMiddleware($configuration->idApiUrl),
        ];

        $middlewaresBusinessApi = [
            new ClientErrorMiddleware(),
            new InternalServerMiddleware(),
            new UrlMiddleware($configuration->businessApiUrl),
        ];

        return new self(
            $client,
            new SymfonySerializer($normalizers, [new JsonEncoderSymfony()]),
            $configuration,
            $middlewaresIdApi,
            $middlewaresBusinessApi,
        );
    }

    public function withClient(PsrHttpClient $client): self
    {
        return new self(
            client: $client,
            serializer: $this->serializer,
            configuration: $this->configuration,
            middlewaresIdApi: $this->middlewaresIdApi,
            middlewaresBusinessApi: $this->middlewaresBusinessApi,
        );
    }

    public function withSerializer(Serializer $serializer): self
    {
        return new self(
            client: $this->client,
            serializer: $serializer,
            configuration: $this->configuration,
            middlewaresIdApi: $this->middlewaresIdApi,
            middlewaresBusinessApi: $this->middlewaresBusinessApi,
        );
    }

    public function withConfiguration(ConfigurationClient $configuration): self
    {
        return new self(
            client: $this->client,
            serializer: $this->serializer,
            configuration: $configuration,
            middlewaresIdApi: $this->middlewaresIdApi,
            middlewaresBusinessApi: $this->middlewaresBusinessApi,
        );
    }

    public function addMiddleware(Middleware $middleware): self
    {
        return new self(
            client: $this->client,
            serializer: $this->serializer,
            configuration: $this->configuration,
            middlewaresIdApi: array_merge($this->middlewaresIdApi, [$middleware]),
            middlewaresBusinessApi: array_merge($this->middlewaresBusinessApi, [$middleware]),
        );
    }

    /**
     * @param non-empty-list<Middleware> $middlewares
     */
    public function withMiddlewares(array $middlewares): self
    {
        return new self(
            client: $this->client,
            serializer: $this->serializer,
            configuration: $this->configuration,
            middlewaresIdApi: $middlewares,
            middlewaresBusinessApi: $middlewares,
        );
    }

    public function createUserClient(): UserClient
    {
        return new RestUserClient(
            new HttpClient(
                $this->configuration,
                new PipelineMiddleware($this->middlewaresIdApi, $this->client),
            ),
            $this->serializer,
            $this->configuration
        );
    }

    public function createDocumentClient(): DocumentClient
    {
        return new RestDocumentClient(
            new HttpClient(
                $this->configuration,
                new PipelineMiddleware($this->middlewaresBusinessApi, $this->client),
            ),
            $this->serializer,
        );
    }

    public function createUserStatusClient(): UserStatusClient
    {
        return new RestUserStatusClient(
            new HttpClient(
                $this->configuration,
                new PipelineMiddleware($this->middlewaresBusinessApi, $this->client),
            ),
            $this->serializer,
        );
    }

    /**
     * @param non-empty-string $baseUri
     * @param non-empty-string $redirectUri
     */
    public function createAuthUrlBuilder(string $baseUri, string $redirectUri): AuthorizationUrlBuilder
    {
        return new AuthorizationUrlBuilder(
            $baseUri,
            $this->configuration->clientId,
            $redirectUri,
        );
    }
}
