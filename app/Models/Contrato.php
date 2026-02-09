<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasLastUser;
use App\Traits\TracksHistory;

class Contrato extends Model
{
    use HasLastUser;
    use TracksHistory;

    protected $fillable = [
        'numero_contrato',
        'data_inicio',
        'data_fim',
        'ativo',
        'last_user_id',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean', 
    ];

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_contrato');
    }

    public function anexos()
    {
        return $this->morphMany(Anexo::class, 'anexable');
    }
}