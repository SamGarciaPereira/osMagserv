<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLastUser;
use App\Traits\TracksHistory;

class ContasPagar extends Model
{
    use HasLastUser;
    use TracksHistory;

    protected $fillable = [
        'fornecedor',
        'descricao',
        'danfe',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'fixa',
        'last_user_id',
    ];
    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'fixa' => 'boolean',
    ];

    public function anexos()
    {
        return $this->morphMany(Anexo::class, 'anexable');
    }
}
