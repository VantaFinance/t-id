<?php

declare(strict_types=1);

namespace Vanta\Integration\TId;

use Psr\Http\Client\ClientInterface as PsrHttpClient;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
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
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\SandboxBusinessClientMiddleware;
use Vanta\Integration\TId\Infrastructure\Serializer\Normalizer\PhoneNumberNormalizer;
use Vanta\Integration\TId\Transport\RestBusinessClient;
use Vanta\Integration\TId\Transport\RestIdClient;

final readonly class RestClientBuilder
{
    /**
     * @param non-empty-list<Middleware> $middlewares
     */
    private function __construct(
        private PsrHttpClient $client,
        public Serializer $serializer,
        private ConfigurationClient $configuration,
        private array $middlewares,
    ) {
    }

    public static function create(ConfigurationClient $configuration, PsrHttpClient $client): self
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $objectNormalizer     = new ObjectNormalizer(
            $classMetadataFactory,
            new MetadataAwareNameConverter($classMetadataFactory),
            null,
            new PropertyInfoExtractor(
                [],
                [new PhpStanExtractor()],
                [],
                [],
                []
            ),
            new ClassDiscriminatorFromClassMetadata($classMetadataFactory),
        );

        $normalizers = [
            new BackedEnumNormalizer(),
            new UidNormalizer(),
            new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => '!Y-m-d']),
            new PhoneNumberNormalizer(),
            new UnwrappingDenormalizer(),
            $objectNormalizer,
            new ArrayDenormalizer(),
        ];

        $middlewares = [
            new ClientErrorMiddleware(),
            new InternalServerMiddleware(),
        ];

        return new self(
            $client,
            new SymfonySerializer($normalizers, [new JsonEncoderSymfony()]),
            $configuration,
            $middlewares
        );
    }

    public function withSerializer(Serializer $serializer): self
    {
        return new self(
            client: $this->client,
            serializer: $serializer,
            configuration: $this->configuration,
            middlewares: $this->middlewares
        );
    }

    public function withConfiguration(ConfigurationClient $configuration): self
    {
        return new self(
            client: $this->client,
            serializer: $this->serializer,
            configuration: $configuration,
            middlewares: $this->middlewares
        );
    }

    public function addMiddleware(Middleware $middleware): self
    {
        return new self(
            client: $this->client,
            serializer: $this->serializer,
            configuration: $this->configuration,
            middlewares: array_merge($this->middlewares, [$middleware])
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
            middlewares: $middlewares
        );
    }

    public function createIdClient(): IdClient
    {
        return new RestIdClient(
            new HttpClient(
                $this->configuration,
                new PipelineMiddleware($this->middlewares, $this->client),
            ),
            $this->serializer,
            $this->configuration
        );
    }

    public function createBusinessClient(): BusinessClient
    {
        return new RestBusinessClient(
            new HttpClient(
                $this->configuration,
                new PipelineMiddleware($this->middlewares, $this->client),
            ),
            $this->serializer,
            $this->configuration
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
            $redirectUri
        );
    }
}
