<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient\Middleware;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;

final readonly class UrlMiddleware implements Middleware
{
    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
    {
        $request = $request->withUri(
            Utils::uriFor(sprintf('%s%s', $configuration->url, $request->getUri()->__toString()))
        );

        return $next($request, $configuration);
    }
}
