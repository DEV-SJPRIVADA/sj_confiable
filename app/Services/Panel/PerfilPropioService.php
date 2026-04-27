<?php

declare(strict_types=1);

namespace App\Services\Panel;

use App\Models\Persona;
use Illuminate\Support\Facades\DB;

final class PerfilPropioService
{
    /**
     * @param  array{paterno: string, materno: ?string, nombre: string, telefono: ?string, correo: string, celular: string, direccion: ?string}  $data
     */
    public function update(Persona $persona, array $data): Persona
    {
        return DB::transaction(function () use ($persona, $data): Persona {
            $persona->paterno = $data['paterno'];
            $persona->materno = $this->emptyToNull($data['materno'] ?? null);
            $persona->nombre = $data['nombre'];
            $persona->telefono = $this->emptyToNull($data['telefono'] ?? null);
            $persona->celular = $data['celular'];
            $persona->correo = $data['correo'];
            $persona->direccion = $this->emptyToNull($data['direccion'] ?? null);
            $persona->save();

            return $persona->refresh();
        });
    }

    private function emptyToNull(?string $v): ?string
    {
        if ($v === null) {
            return null;
        }
        $t = trim($v);

        return $t === '' ? null : $t;
    }
}
