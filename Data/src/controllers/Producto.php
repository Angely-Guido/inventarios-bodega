<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Models\Producto as ProductoModel;
use Illuminate\Database\QueryException;


class Producto  {
    private ContainerInterface $container;
    public function __construct(ContainerInterface $c) {
        $this->container = $c;
    }


    public function read(Request $request, Response $response, array $args) {

        $this->container->get('eloquent');

        if (isset($args['id'])) {
            $producto = ProductoModel::query()
                ->select(['id', 'sku', 'nombre_producto', 'descripcion', 'precio_unitario', 'stock_actual', 'categoria', 'updated_at'])
                ->find($args['id']);

                if ($producto === null) {
                    return $this->json($response, ['error' => 'Producto no encontrado.'], 404);
            }

            return $this->json($response, ['data' => $producto], 200);
        }

        $producto = ProductoModel::query()
            ->select(['id', 'sku', 'nombre_producto', 'descripcion', 'precio_unitario', 'stock_actual', 'categoria', 'updated_at'])
            ->get()
            ->toArray();

        return $this->json($response, ['data' => $producto], 200);
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];

        $sku = trim((string) ($data['sku']  ?? ''));
        $nombre_producto = trim((string) ($data['nombre_producto'] ?? ''));
        $descripcion = isset($data['descripcion']) ? trim((string) $data['descripcion']) : null;
        $precio_unitario = trim((string) ($data['precio_unitario'] ?? ''));
        $stock_actual = trim((string) ($data['stock_actual'] ?? ''));
        $categoria = (string) ($data['categoria'] ?? '');

        try {
            $eloquent = $this->container->get('eloquent');
                    $producto = ProductoModel::create([
                        'sku' => $sku,
                        'nombre_producto' => $nombre_producto,
                        'descripcion' => $descripcion,
                        'precio_unitario' => $precio_unitario,
                        'stock_actual' => $stock_actual,
                        'categoria' => $categoria
                    ]);
            
        } 
        catch (QueryException $exception) {

            if ( (int) ($exception->errorInfo[1] ?? 0) === 1062    ) {
                return $this->json($response, ['error' => 'La sku ya está registrada.'], 409);
            }
            throw $exception;
        }
        return $this->json($response, ['data' => $producto->toArray()], 201);
    }


public function update(Request $request, Response $response, array $args): Response {

        $eloquent = $this->container->get('eloquent');
        if (isset($args['id'])) {
            $producto = ProductoModel::find($args['id']);

            if ($producto === null) {
                return $this->json($response, ['error' => 'Producto no encontrado.'], 404);
            }

            $data = $request->getParsedBody();
            $data = is_array($data) ? $data : [];

            if (isset($data['nombre_producto'])) {
                $nombreproducto = trim((string) $data['nombre_producto']);
                
                if ($nombreproducto === '' || mb_strlen($nombreproducto) > 150) {
                    return $this->json($response, ['error' => 'Nombre producto inválido.'], 422);
                }
                $producto->nombre_producto = $nombreproducto;
            }

            $producto->save();

            return $this->json($response, ['data' => $producto->toArray()], 200);
            }
        return $this->json($response, ['error' => 'Datos incompletos'], 400);
    }


public function delete(Request $request, Response $response, array $args): Response
    {
        $eloquent = $this->container->get('eloquent');
        if (isset($args['id'])) {
            $producto = ProductoModel::find($args['id']);

            if ($producto === null) {
                return $this->json($response, ['error' => 'Producto no encontrado.'], 404);
            }
            $producto->delete();
            return $this->json($response, ['message' => 'Producto eliminado exitosamente'], 200);
        }
    return $this->json($response, ['error' => 'Datos incompletos'], 400);
    }

private function json(Response $response, array $payload, int $status): Response {
        $response->getBody()->write(json_encode($payload,
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($status);
    }


}
