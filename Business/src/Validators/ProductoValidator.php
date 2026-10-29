<?php
declare (strict_types=1);

namespace App\Validators;

use Respect\Validation\ValidatorBuilder as v;

final class ProductoValidator {
    private function reglas() : array {
        return [
            'sku' => [
                'regla' => v::stringType()->notBlank()->length(v::between(1, 50)),
                'mensaje' => 'La sku es obligatoria y debe tener entre 1 y 50 caracteres.',
            ],
            'nombre_producto' => [
                'regla' => v::stringType()->notBlank()->length(v::between(2, 100)),
                'mensaje' => 'El nombre del producto es obligatorio y debe tener entre 2 y 100 caracteres.',
            ],
        ];
    }

    public function validate(array $data): array {
        $errores = [];

        foreach ($this->reglas() as $campo => $config) {
            $valor = $data[$campo] ?? null;
            if ($config['regla']->validate($valor)->hasFailed()) {
                $errores[$campo] = $config['mensaje'];
            }
        }

        return $errores;
    }
}