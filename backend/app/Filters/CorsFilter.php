<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle preflight OPTIONS requests
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $response = service('response');
            $this->setCorsHeaders($response);
            return $response->setStatusCode(200);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Inject CORS headers into normal responses (GET, POST, etc.)
        $this->setCorsHeaders($response);
        return $response;
    }

    private function setCorsHeaders(ResponseInterface $response)
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowedOriginsStr = env('CORS_ALLOWED_ORIGINS') ?: getenv('CORS_ALLOWED_ORIGINS') ?: '';
        $allowedOrigins = array_filter(array_map('trim', explode(',', $allowedOriginsStr)));
        $frontendUrl = env('FRONTEND_URL') ?: getenv('FRONTEND_URL');
        if ($frontendUrl) {
            $allowedOrigins[] = rtrim($frontendUrl, '/');
        }
        $allowedOrigins = array_merge($allowedOrigins, [
            'http://localhost:5173',
            'http://localhost:5174',
            'https://gad-ams.me',
            'https://www.gad-ams.me',
            'https://bsugad.com',
            'https://www.bsugad.com'
        ]);

        if (!empty($origin) && in_array($origin, $allowedOrigins, true)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
        } elseif (!empty($origin) && (str_contains($origin, 'localhost') || str_contains($origin, '127.0.0.1') || str_contains($origin, 'app.github.dev'))) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
        } else {
            $response->setHeader('Access-Control-Allow-Origin', $frontendUrl ?: ($origin ?: '*'));
        }

        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, X-User-Id');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Max-Age', '86400');
    }
}