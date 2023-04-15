<?php

declare(strict_types=1);

namespace App;

use function file_get_contents;
use function http_build_query;
use function json_decode;
use function json_encode;
use function stream_context_create;

class HttpRequest
{   
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function get(string $endpoint, array $parameters = null): array
    {
        return $this->call('GET', $endpoint, $parameters);
    }

    public function post(string $endpoint, array $data = null): array
    {
        return $this->call('POST', $endpoint, null, $data);
    }

    public function put(string $endpoint, array $data = null): array
    {
        return $this->call('PUT', $endpoint, null, $data);
    }

    public function delete(string $endpoint): array
    {
        return $this->call('DELETE', $endpoint);
    }

    private function call(string $method, string $endpoint, array $parameters = null, array $data = null): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => 'Content-type: application/json',
                'content' => $data ? json_encode($data) : null
            ]
        ];
      
        $url .= ($parameters ? '?' . http_build_query($parameters) : '');
        
        $response = file_get_contents($url, false, stream_context_create($opts));
        
        return json_decode($response, true);
    }
}
