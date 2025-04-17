<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient\Middleware;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as PsrHttpClient;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;

final readonly class PipelineMiddleware
{
    /**
     * @param array<int, Middleware> $middlewares
     */
    public function __construct(
        private array $middlewares,
        private PsrHttpClient $client
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function process(Request $request, ConfigurationClient $configuration): Response
    {
        $middlewares = $this->middlewares;
        $middleware  = array_shift($middlewares);

        // @todo выкосить echo
        if (null == $middleware) {
//            echo 'request: ' . $request->getMethod() . ' ' . $request->getUri()->__toString() . ' ' . $request->getBody()->getContents() . PHP_EOL;
//            $request->getBody()->rewind();


            $response = $this->client->sendRequest($request);


//            echo 'response: ' . $response->getStatusCode() . ' ' . $response->getBody()->getContents() . PHP_EOL . PHP_EOL . PHP_EOL;
//            $response->getBody()->rewind();

            return $response;
        }

        return $middleware->process($request, $configuration, [new self($middlewares, $this->client), 'process']);
    }
}
