<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 't_clientes';

    protected $primaryKey = 'id_cliente';

    public $incrementing = true;

    public $timestamps = false;

    protected $guarded = ['id_cliente'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_cliente', 'id_cliente');
    }
}
