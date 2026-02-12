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
        'status_documentos',
        'observacoes',
        'doc_ordem_servico',
        'doc_ficha_epi',
        'doc_nr06',
        'doc_nr10',
        'doc_nr12',
        'doc_nr18',
        'doc_nr35',
        'doc_aso',
        'doc_contrato_intermitente',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_admissao' => 'date',
        'data_demissao' => 'date',
        'ativo' => 'boolean',
        'doc_ordem_servico' => 'date',
        'doc_ficha_epi' => 'date',
        'doc_nr06' => 'date',
        'doc_nr10' => 'date',
        'doc_nr12' => 'date',
        'doc_nr18' => 'date',
        'doc_nr35' => 'date',
        'doc_aso' => 'date',
        'doc_contrato_intermitente' => 'date',
    ];

    public function getIdadeAttribute()
    {
        if ($this->data_nascimento) {
            return $this->data_nascimento->age;
        }
        return null;
    }

    public function getVencimentoOrdemServicoAttribute(){
        return $this->doc_ordem_servico ? $this->doc_ordem_servico->addYears(1) : null;
    }

    public function getVencimentoFichaEpiAttribute(){
        return $this->doc_ficha_epi ? $this->doc_ficha_epi->addYears(1) : null;
    }

    public function getVencimentoNr06Attribute(){
        return $this->doc_nr06 ? $this->doc_nr06->addYears(1) : null;
    }

    public function getVencimentoNr10Attribute()
    {
        return $this->doc_nr10 ? $this->doc_nr10->addYears(2) : null;
    }

    public function getVencimentoNr12Attribute(){
        return $this->doc_nr12 ? $this->doc_nr12->addYears(1) : null;
    }

    public function getVencimentoNr18Attribute()
    {
        return $this->doc_nr18 ? $this->doc_nr18->addYears(2) : null;
    }

    public function getVencimentoNr35Attribute()
    {
        return $this->doc_nr35 ? $this->doc_nr35->addYears(2) : null;
    }

    public function getVencimentoAsoAttribute(){
        return $this->doc_aso ? $this->doc_aso->addYears(1) : null;
    }

    public function getVencimentoContratoIntermitenteAttribute(){
        return $this->doc_contrato_intermitente ? $this->doc_contrato_intermitente->addYears(1) : null;
    }


    public function anexos()
    {
        return $this->morphMany(Anexo::class, 'anexable');
    }
}
