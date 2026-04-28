<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacionCliente extends Model
{
    protected $table = 'notificaciones_cliente';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'leido' => 'boolean',
            'fecha' => 'datetime',
        ];
    }

    public function usuarioDestino(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_destino', 'id_usuario');
    }

    public function scopeNoLeidas(Builder $query): Builder
    {
        return $query->where(static function (Builder $q): void {
            $q->where('leido', 0)->orWhereNull('leido');
        });
    }
}
