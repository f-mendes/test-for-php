<?php

declare(strict_types=1);

namespace App\Http;

class HttpResponse 
{   
    private $httpCode;
    private $headers;
    private $body;

    
    public function __construct(int $httpCode, array $headers, array $body)
    {
        $this->httpCode = $httpCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function gethttpCode(): int 
    {
        return $this->httpCode;
    }

    public function getHeaders(): array 
    {
        return $this->headers;
    }

    public function getBody(): array 
    {
        return $this->body;
    }
}