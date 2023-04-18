<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\HttpRequest;
use App\Http\HttpResponse;

class RequestTest extends TestCase
{
    public string $baseUrl = 'https://jsonplaceholder.typicode.com/';

    public function testGet(): void
    {
        $request = new HttpRequest($this->baseUrl);
        $response = $request->get('posts/1');

        $this->assertEquals(200, $response->gethttpCode());
        $this->assertInstanceOf(HttpResponse::class, $response);
      
    }

    public function testGetNotFound(): void
    {
        $request = new HttpRequest($this->baseUrl);
        $response = $request->get('postss/1');

        $this->assertEquals(404, $response->gethttpCode());
        $this->assertInstanceOf(HttpResponse::class, $response);
      
    }

    public function testPost(): void
    {
        $request = new HttpRequest($this->baseUrl);
        
        $data = [
            'title' => 'autodoc',
            'body' => 'cache system',
            'userId' => 1 
        ];
        $response = $request->post('posts', $data);

        $this->assertEquals(201, $response->gethttpCode());
        $this->assertInstanceOf(HttpResponse::class, $response);
      
    }

    public function testPut(): void
    {
        $request = new HttpRequest($this->baseUrl);
        
         $data = [
            'id' => 1,
            'title' => 'autodoc',
            'body' => 'cache system',
            'userId' => 1 
        ];
        $response = $request->put('posts/1', $data);
        $body = $response->getBody();

        $this->assertEquals(200, $response->gethttpCode());

        $this->assertIsArray($body);
        $this->assertArrayHasKey('title', $body);
        $this->assertEquals('autodoc', $body['title']);

        $this->assertInstanceOf(HttpResponse::class, $response);
      
    }

    public function testDelete(): void
    {
        $request = new HttpRequest($this->baseUrl);
        
        $response = $request->delete('posts/1');
        
        $this->assertEquals(200, $response->gethttpCode());
        $this->assertInstanceOf(HttpResponse::class, $response);
      
    }

    public function testClearCache(): void
    {
        $request = new HttpRequest($this->baseUrl);
        
        $this->assertTrue($request->clear());
      
    }

}
