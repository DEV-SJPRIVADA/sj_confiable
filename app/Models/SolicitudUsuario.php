<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Solicitudes de gestión de usuarios (cliente solicita alta/baja/modificación de usuarios).
 *
 * @property int $id_solicitud
 * @property int $id_cliente
 * @property int $id_usuario_solicitante
 * @property string $tipo
 * @property string $datos_usuario
 * @property string $estado
 */
class SolicitudUsuario extends Model
{
    protected $table = 't_solicitudes_usuario';

    protected $primaryKey = 'id_solicitud';

    public $incrementing = true;

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'datetime',
            'fecha_respuesta' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_solicitante', 'id_usuario');
    }

    public function usuarioResponde(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_responde', 'id_usuario');
    }
}
