<?php
namespace App\controllers;

use App\controllers\Producto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteCollectorProxy;
$app->get('/', function (Request $request, Response $response, array $args) {
    
    $response->getBody()->write("Hello Slim");
    return $response;
});

$app->group('/api', function(RouteCollectorProxy $api) { 
    $api->group('/productos', function(RouteCollectorProxy $url) {
        $url->get('[/{id}]', Producto::class . ':read');
        $url->post('', Producto::class . ':create');
        $url->put('/{id}', Producto::class . ':update');
        $url->delete('/{id}', Producto::class . ':delete');

});
});