<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLastUser;
use App\Traits\TracksHistory;

class Manutencao extends Model
{
    use HasLastUser;
    use TracksHistory;

    protected $table = 'manutencoes';

    protected $fillable = [
        'cliente_id',
        'chamado',
        'solicitante',
        'descricao',
        'data_inicio_atendimento',
        'data_fim_atendimento',
        'tipo',
        'status',   
        'last_user_id'
    ];

    protected $casts = [
        'data_inicio_atendimento' => 'date',
        'data_fim_atendimento' => 'date',
    ];

     public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function anexos()
    {
        return $this->morphMany(Anexo::class, 'anexable');
    }
    
    
}
