<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HasLastUser;

class Cliente extends Model
{
    use HasFactory;
    use HasLastUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'matriz_id',
        'nome',
        'documento',
        'responsavel',
        'email',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'last_user_id',
    ];

    public function matriz(){
        return $this->belongsTo(Cliente::class, 'matriz_id');
    }

    public function filiais(){
        return $this->hasMany(Cliente::class, 'matriz_id');
    }

    public function contratos()
    {
        return $this->belongsToMany(Contrato::class, 'cliente_contrato');
    }

    public function contratoAtivo()
    {
        $contratoDireto = $this->belongsToMany(Contrato::class, 'cliente_contrato')
                    ->where('ativo', true)
                    ->latest()
                    ->first();
        
        if($contratoDireto){
            return $contratoDireto;
        }

        if ($this->matriz_id) {
            return $this->matriz->contratoAtivo(); 
        }

        return null;
    }
    
    public function getContratoVigenteAttribute()
    {
        return $this->contratoAtivo();
    }

    public function anexos()
    {
        return $this->morphMany(Anexo::class, 'anexable');
    }
}