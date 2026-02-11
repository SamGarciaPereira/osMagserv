<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLastUser;
use App\Traits\TracksHistory;

class Funcionario extends Model
{
    use HasFactory, SoftDeletes, HasLastUser, TracksHistory;

    protected $fillable = [
        'nome',
        'cpf',
        'rg',
        'data_nascimento',
        'estado_nascimento',
        'cidade_nascimento',
        'estado_civil',
        'sexo',
        'numero_filhos',
        'foto_perfil',
        'email',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cargo',
        'tipo_contrato',
        'data_admissao',
        'data_demissao',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_admissao' => 'date',
        'data_demissao' => 'date',
        'ativo' => 'boolean',
    ];

    public function getIdadeAttribute()
    {
        if ($this->data_nascimento) {
            return $this->data_nascimento->age;
        }
        return null;
    }

    public function anexos()
    {
        return $this->morphMany(Anexo::class, 'anexable');
    }
}
