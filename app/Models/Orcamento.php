<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasLastUser;

class Orcamento extends Model
{
    use HasFactory;
    use HasLastUser;

    public $numero_manual;

    protected $fillable = [
        'cliente_id',
        'data_solicitacao',
        'numero_proposta',
        'data_envio',
        'data_aprovacao',
        'escopo',
        'comentario',
        'checklist',
        'valor',
        'revisao',
        'status',
        'last_user_id',
    ];

    protected $casts = [
        'data_solicitacao' => 'date',
        'data_envio' => 'date',
        'data_aprovacao' => 'date',
        'valor' => 'decimal:2',
        'checklist' => 'array',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function processo(){
        return $this->hasOne(Processo::class);
    }

    public function anexos()
    {
        return $this->morphMany(Anexo::class, 'anexable');
    }
}
