<?php

declare(strict_types=1);

namespace App\Http;

use App\Services\MemoryCache;
use App\Services\FileCache;
use App\Http\HttpResponse;

use function file_get_contents;
use function http_build_query;
use function intval;
use function json_decode;
use function json_encode;
use function preg_match;
use function stream_context_create;


class HttpRequest extends FileCache
{   
    /** 
     *  O atributo  baseUrl serve para deinir a url base da API
     */
    private string $baseUrl;

    /**
     * O atributo ttl defini um tempo de vida padrão de 1 hora para o cache, 
     * mas pode ser alterado e acessado através dos métodos setTtl e getTtl
     */
    private int $ttl = 3600;


    public function __construct(string $baseUrl)
    { 
        $this->baseUrl = $baseUrl;
    }


    public function get(string $endpoint, array $parameters = null): object
    {
        $endpoint = $this->buildEndPoint($endpoint, $parameters);
        $data = $this->pull($endpoint);

        /**
         * Essa condição verifica se o array do cache retornou vazio
         * Se verdadeiro busca os dados na fonte original e armazena no cache
         */
        if(empty($data)){
            
            $data = $this->call('GET', $endpoint);

            //Armazena os dados no cache caso a requisição seja valida
            if ($data['httpCode'] >= 200 || $data['httpCode'] < 300)
                $this->set($endpoint, $data, $this->ttl);      
        }

        return new HttpResponse($data['httpCode'], $data['header'], $data['data']);
    }

    public function post(string $endpoint, array $data = null): object
    {   
 
        $endpoint = $this->buildEndPoint($endpoint);
        $data = $this->call('POST', $endpoint, $data);
            
        return new HttpResponse($data['httpCode'], $data['header'], $data['data']);
    }

    public function put(string $endpoint,array $data = null): object
    {   /**
         * Para atualização primeiro é removido os dados do cache,
         * em seguida busca os dados na fonte original 
         * e por último atualiza o cache
         */

        $endpoint = $this->buildEndPoint($endpoint);
        $this->remove($endpoint);
        $data = $this->call('PUT', $endpoint, $data);
        
        //Armazena os dados no cache caso a requisição seja valida
        if ($data['httpCode'] >= 200 || $data['httpCode'] < 300)
            $this->set($endpoint, $data, $this->ttl);   

        
        return new HttpResponse($data['httpCode'], $data['header'], $data['data']);
     
    }

    public function delete(string $endpoint): object
    {   
        $endpoint = $this->buildEndPoint($endpoint);
        $this->remove($endpoint);
        $data = $this->call('DELETE', $endpoint);
                
        return new HttpResponse($data['httpCode'], $data['header'], $data['data']);
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    private function buildEndPoint(string $endpoint, array $parameters = null): string 
    {
        $endpoint = ltrim($endpoint, '/');
        $endpoint .= ($parameters ? '?' . http_build_query($parameters) : '');
        return $endpoint;
    }

    private function call(string $method, string $endpoint, array $data = null): array
    {
        
        $url = rtrim($this->baseUrl, '/') . '/' . $endpoint;

        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => 'Content-type: application/json',
                'content' => $data ? json_encode($data) : null,
                'ignore_errors' => true, //Defini esse parâmetro para ignorar o warning em status code do tipo 404
            ]
        ];
        
        
        $response = file_get_contents($url, false, stream_context_create($opts));

        /**
         * Utilizei essa função para pegar o http code
         */
        preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$http_response_header[0], $out );
        $httpCode = intval($out[1]);
        
        return [
            'httpCode' => $httpCode,
            'header' => $http_response_header,
            'data' =>  json_decode($response, true)
        ];
    }
}
