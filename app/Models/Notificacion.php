<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Notificaciones para el panel consultor / admin (tabla `notificaciones`, filtro `rol_destino`).
 *
 * @property int $id
 * @property string $tipo
 * @property string $cliente_nombre
 * @property int $id_solicitud
 * @property string $mensaje
 * @property int $rol_destino
 * @property bool $leido
 * @property \Carbon\CarbonImmutable $fecha
 */
class Notificacion extends Model
{
    protected $table = 'notificaciones';

    public $timestamps = false;

    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'leido' => 'boolean',
            'fecha' => 'datetime',
        ];
    }

    public function scopeForRol(Builder $query, int $idRol): Builder
    {
        return $query->where('rol_destino', $idRol);
    }

    public function scopeNoLeidas(Builder $query): Builder
    {
        return $query->where('leido', 0);
    }
}
