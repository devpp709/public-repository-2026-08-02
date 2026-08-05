<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CorsMiddleware implements HttpKernelInterface
{
    private HttpKernelInterface $app;

    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        // Если это OPTIONS запрос - отвечаем сразу
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response('', 204);
            $this->addCorsHeaders($response, $request);
            return $response;
        }

        $response = $this->app->handle($request, $type, $catch);
        $this->addCorsHeaders($response, $request);

        return $response;
    }

    private function addCorsHeaders(Response $response, Request $request): void
    {
        $origin = $request->headers->get('Origin');

        // Разрешаем все localhost порты
        if ($origin && preg_match('/^http:\/\/localhost:[0-9]+$/', $origin)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } elseif ($origin && preg_match('/^http:\/\/127.0.0.1:[0-9]+$/', $origin)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            // Для разработки - разрешаем все
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }

        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, X-Locale, X-CSRF-Token');
        $response->headers->set('Access-Control-Expose-Headers', 'Content-Length, Content-Range');
        $response->headers->set('Access-Control-Max-Age', '86400');
    }
}
