<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    public $timestamps = false;

    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'fecha_creacion' => 'datetime',
            'fecha_asignacion_proveedor' => 'datetime',
            'fecha_expedicion' => 'date',
        ];
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }

    public function proveedorAsignado(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(CatServicio::class, 'servicio_id', 'id_servicio');
    }

    public function paquete(): BelongsTo
    {
        return $this->belongsTo(PaqueteServicio::class, 'paquete_id', 'id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'solicitud_id', 'id');
    }

    public function evaluados(): HasMany
    {
        return $this->hasMany(Evaluado::class, 'solicitud_id', 'id');
    }

    /**
     * Relación vía `solicitud_servicios` (mismo criterio que v_solicitudes en BD).
     */
    public function serviciosPivote(): BelongsToMany
    {
        return $this->belongsToMany(
            CatServicio::class,
            'solicitud_servicios',
            'solicitud_id',
            'servicio_id',
        );
    }

    public function historialRespuestas(): HasMany
    {
        return $this->hasMany(RespuestaSolicitud::class, 'solicitud_id', 'id')
            ->orderByDesc('fecha_respuesta');
    }

    public function ultimaRespuestaMadre(): HasOne
    {
        return $this->hasOne(RespuestaMadre::class, 'solicitud_id', 'id')
            ->latestOfMany('fecha_creacion');
    }

    public function respuestasMadre(): HasMany
    {
        return $this->hasMany(RespuestaMadre::class, 'solicitud_id', 'id');
    }
}
