<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    /**
     * La tabla en producción no incluye columna remember_token; desactiva "recuérdame".
     *
     * @var string|null
     */
    protected $rememberTokenName = null;

    protected $table = 't_usuarios';

    protected $primaryKey = 'id_usuario';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'id_rol',
        'id_persona',
        'usuario',
        'password',
        'activo',
        'ciudad',
        'fecha_insert',
        'estado_conexion',
        'id_cliente',
        'creado_por',
        'reset_token',
        'reset_token_expiry',
        'id_proveedor',
        'permiso_ver_documentos',
        'permiso_subir_documentos',
        'permiso_crear_solicitudes',
    ];

    protected $hidden = [
        'password',
        'reset_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activo' => 'integer',
            'permiso_ver_documentos' => 'boolean',
            'permiso_subir_documentos' => 'boolean',
            'permiso_crear_solicitudes' => 'boolean',
            'reset_token_expiry' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isActive(): bool
    {
        return (int) $this->activo === 1;
    }

    public function userRole(): ?UserRole
    {
        return UserRole::tryFrom((int) $this->id_rol);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(CatRol::class, 'id_rol', 'id_rol');
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    /**
     * @return HasMany<Solicitud, $this>
     */
    public function solicitudesCreadas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'usuario_id', 'id_usuario');
    }
}
