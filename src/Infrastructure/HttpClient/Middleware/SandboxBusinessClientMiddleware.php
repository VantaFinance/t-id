<?php

declare(strict_types=1);

namespace Vanta\Integration\TId\Infrastructure\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;

final readonly class SandboxBusinessClientMiddleware implements Middleware
{
    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
    {
        $uri = $request->getUri();

        $uri = $uri->withPath(str_replace('/openapi/api/', '/openapi/sandbox/api/', $uri->getPath()));

        $request = $request->withUri($uri);

        return $next($request, $configuration);
    }
}
