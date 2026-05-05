<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacionProveedor extends Model
{
    protected $table = 'notificaciones_proveedor';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'leido' => 'boolean',
            'fecha' => 'datetime',
        ];
    }

    public function proveedorDestino(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor_destino', 'id_proveedor');
    }

    public function scopeNoLeidas(Builder $query): Builder
    {
        return $query->where(static function (Builder $q): void {
            $q->where('leido', 0)->orWhereNull('leido');
        });
    }
}
