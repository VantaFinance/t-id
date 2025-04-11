<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient;

use Psr\Http\Client\ClientInterface as PsrHttpClient;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Vanta\Integration\TId\Infrastructure\HttpClient\Middleware\PipelineMiddleware;

final readonly class HttpClient implements PsrHttpClient
{
    public function __construct(
        private ConfigurationClient $configuration,
        private PipelineMiddleware $pipeline
    ) {
    }

    public function sendRequest(Request $request): Response
    {
        return $this->pipeline->process($request, $this->configuration);
    }
}
