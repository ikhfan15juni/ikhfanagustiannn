<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'cors'], function ($router) {





$router->group(['prefix' => 'Stuff-Stock', 'middleware' => 'auth'],function() use($router){
    // $router->post('store', 'StuffStockController@store');
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
});

$router->group(['prefix' => 'Lending', 'middleware' => 'auth'],function() use($router){
    $router->get('/', 'LendingController@index');
    $router->post('store', 'LendingController@store');
    $router->post('show', 'LendingController@show');
    $router->patch('/update/{id}', 'LendingController@update');
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
});

$router->group(['prefix' => 'Restoration'],function() use($router){
    $router->post('store', 'RestorationController@store');
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
});

$router->group(['prefix' => 'inboud-stuff', 'middleware' => 'auth'],function() use($router){
    $router->post('store', 'InboudstuffController@store');
    $router->get('/', 'InboudstuffController@index');
    $router->patch('/update/{id}', 'InboudstuffController@update');
    $router->post('/delete/{id}', 'InboudstuffController@destroy');
    $router->post('recyle-bin', 'InboudstuffController@recyleBin');
    $router->post('/restore/{id}', 'InboudstuffController@restore');
    $router->get('forec-delete{id}', 'InboudstuffController@forceDestroy');

});



$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/profile', 'AuthController@me');




// $router->get('/stuffs', 'StuffController@index');

// $router->get('/stuffs', 'StuffController@index');

$router->group(['prefix' => 'stuff'  ],function() use ($router){
   $router->get('/', 'StuffController@index');
   $router->post('/store', 'StuffController@store');
   $router->get('/trash', 'StuffController@trash' );
   
   $router->get('{id}', 'StuffController@show' );
   $router->patch('{id}', 'StuffController@update' );
   $router->delete('delete/{id}', 'StuffController@destroy' );
   $router->get('/restore/{id}', 'StuffController@restore' );
   $router->delete('/permanent/{id}', 'StuffController@deletePermanent' );


});


// $router->post('/login', 'UseController@login');
// $router->get('/logout', 'UseController@logout');

$router->get('/users', 'UseController@index');

$router->group(['prefix' => 'User'],function() use ($router){
    $router->get('/', 'UseController@index');
    $router->post('/', 'UseController@store');
    $router->get('detail/{id}', 'UseController@trash' );
    $router->get('update/{id}', 'UseController@trash' );
   
    $router->get('{id}', 'StuffController@show' );
    $router->patch('{id}', 'StuffController@update' );
    $router->delete('{id}', 'StuffController@destroy' );
    $router->get('/restore/{id}', 'StuffController@restore' );
    $router->delete('/permanent/{id}', 'StuffController@deletePermanent' );
    
});

}); 

