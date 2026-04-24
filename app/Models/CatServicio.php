<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatServicio extends Model
{
    protected $table = 't_cat_servicio';

    protected $primaryKey = 'id_servicio';

    public $incrementing = true;

    public $timestamps = false;

    protected $guarded = ['id_servicio'];

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'servicio_id', 'id_servicio');
    }
}
