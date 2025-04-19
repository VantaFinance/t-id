<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\Infrastructure\HttpClient\Exception\InternalServerErrorException;
use Yiisoft\Http\Status;

final readonly class InternalServerMiddleware implements Middleware
{
    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
    {
        $response   = $next($request, $configuration);
        $statusCode = $response->getStatusCode();

        if (!(Status::INTERNAL_SERVER_ERROR <= $statusCode && $statusCode <= Status::NETWORK_AUTHENTICATION_REQUIRED)) {
            return $response;
        }

        throw InternalServerErrorException::create($response, $request);
    }
}
