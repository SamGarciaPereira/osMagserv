<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLastUser;
use App\Traits\TracksHistory;

class ContasReceber extends Model
{
    use HasLastUser;
    use TracksHistory;

    protected $fillable = [
        'processo_id',
        'cliente_id',
        'descricao',
        'nf',
        'valor',
        'data_vencimento',
        'data_recebimento',
        'status',
        'last_user_id',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_recebimento' => 'date'
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function anexos(){
        return $this->morphMany(Anexo::class, 'anexable');
    }
}
