<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    protected $fillable = [
        'nome_original',
        'caminho',
        'anexable_id',
        'anexable_type',
        'is_confidencial',
    ];

    protected $touches = ['anexable'];

    public function anexable()
    {
        return $this->morphTo();
    }
}
