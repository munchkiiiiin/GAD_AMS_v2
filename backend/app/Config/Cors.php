<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cross-Origin Resource Sharing (CORS) Configuration
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 */
class Cors extends BaseConfig
{
    /**
     * The default CORS configuration.
     *
     * @var array{
     *      allowedOrigins: list<string>,
     *      allowedOriginsPatterns: list<string>,
     *      supportsCredentials: bool,
     *      allowedHeaders: list<string>,
     *      exposedHeaders: list<string>,
     *      allowedMethods: list<string>,
     *      maxAge: int,
     *  }
     */
    public array $default = [
        'allowedOrigins'         => [
            'https://gad-ams.me',
            'https://www.gad-ams.me',
            'https://bsugad.com',
            'https://www.bsugad.com',
            'http://localhost:5173',
            'http://localhost:5174'
        ],
        'allowedOriginsPatterns' => [],
        'allowedHeaders'         => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'X-User-Id'],
        'allowedMethods'         => ['GET', 'POST', 'OPTIONS', 'PUT', 'DELETE'],
        'exposedHeaders'         => [],
        'maxAge'                 => 7200,
        'hosts'                  => [],
    ];

    public function __construct()
    {
        parent::__construct();

        // Dynamically add allowed origins from environment
        $extraOrigins = env('CORS_ALLOWED_ORIGINS') ?: getenv('CORS_ALLOWED_ORIGINS');
        if (!empty($extraOrigins)) {
            $parsed = array_filter(array_map('trim', explode(',', $extraOrigins)));
            $this->default['allowedOrigins'] = array_merge($this->default['allowedOrigins'], $parsed);
        }

        $frontendUrl = env('FRONTEND_URL') ?: getenv('FRONTEND_URL');
        if (!empty($frontendUrl)) {
            $this->default['allowedOrigins'][] = rtrim($frontendUrl, '/');
        }

        $this->default['allowedOrigins'] = array_values(array_unique($this->default['allowedOrigins']));
    }
}
