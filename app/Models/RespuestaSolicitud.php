<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\HistorialRespuestaCanal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaSolicitud extends Model
{
    protected $table = 'respuesta_solicitudes';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'fecha_respuesta' => 'datetime',
            'canal' => HistorialRespuestaCanal::class,
        ];
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id', 'id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }
}
