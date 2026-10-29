<?php
namespace App\controllers;

use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function(RouteCollectorProxy $api) {
    $api->group('/productos', function(RouteCollectorProxy $url) {
        $url->get('[/{id}]', Producto::class . ':read');
        $url->post('', Producto::class . ':create');
        $url->put('/{id}', Producto::class . ':update');
        $url->delete('/{id}', Producto::class . ':delete');
    });
});