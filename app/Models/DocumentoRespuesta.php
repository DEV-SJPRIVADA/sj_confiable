<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoRespuesta extends Model
{
    protected $table = 'documentos_respuesta';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'fecha_subidaResp' => 'datetime',
            'visible_para_cliente' => 'boolean',
        ];
    }

    public function respuestaMadre(): BelongsTo
    {
        return $this->belongsTo(RespuestaMadre::class, 'respuesta_madre_id', 'id');
    }
}
