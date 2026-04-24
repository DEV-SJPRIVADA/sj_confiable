<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaqueteServicio extends Model
{
    protected $table = 't_paquetes_servicio';

    public $incrementing = true;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'paquete_id', 'id');
    }
}
