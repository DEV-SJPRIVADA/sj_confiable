<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 't_proveedores';

    protected $primaryKey = 'id_proveedor';

    public $incrementing = true;

    public $timestamps = false;

    protected $guarded = ['id_proveedor'];

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'id_proveedor', 'id_proveedor');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_proveedor', 'id_proveedor');
    }
}
