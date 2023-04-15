<?php

use App\HttpRequest;

require __DIR__.'/../vendor/autoload.php';

$request = new HttpRequest('https://jsonplaceholder.typicode.com/');


//GET
$response = $request->get('posts/1');

//$response = $request->get('posts?userId=1');


//POST

// $data = [
//     'title' => 'teste',
//     'body' => 'cache',
//     'userId' => 1 
// ];

//$response = $request->post('posts', $data);


//PUT 

// $data = [
//     'id' => 1,
//     'title' => 'teste2',
//     'body' => 'cache2',
//     'userId' => 1 
// ];

//$response = $request->put('posts/1',$data);


// DELETE
//$response = $request->delete('posts/1');


echo '<pre>';
var_dump($response);