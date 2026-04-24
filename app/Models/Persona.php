<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $table = 't_persona';

    protected $primaryKey = 'id_persona';

    public $incrementing = true;

    public $timestamps = false;

    protected $guarded = ['id_persona'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_persona', 'id_persona');
    }
}
