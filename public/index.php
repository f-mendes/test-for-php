<?php

use App\Http\HttpRequest;

require __DIR__.'/../vendor/autoload.php';


$request = new HttpRequest('https://jsonplaceholder.typicode.com/');


//GET
// $response = $request->get('posts/1');
// echo '<pre>';
// var_dump($response->getBody());
// var_dump($response->gethttpCode());
// var_dump($response->getHeaders());


//POST

// $data = [
//     'title' => 'teste',
//     'body' => 'cache',
//     'userId' => 1 
// ];

// $response = $request->post('posts', $data);
// echo '<pre>';
// var_dump($response->getBody());
// var_dump($response->gethttpCode());
// var_dump($response->getHeaders());


//PUT 

// $data = [
//     'id' => 1,
//     'title' => 'teste2',
//     'body' => 'cache2',
//     'userId' => 1 
// ];

// $request->put('posts/1',$data);

// $response = $request->get('posts/1');
// echo '<pre>';
// var_dump($response->getBody());
// var_dump($response->gethttpCode());
// var_dump($response->getHeaders());


// DELETE
// $request->delete('posts/1');
// $request->clear();



