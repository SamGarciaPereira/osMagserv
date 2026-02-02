<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasLastUser;
use App\Traits\TracksHistory;

class Processo extends Model
{
    use HasFactory;
    use HasLastUser;
    use TracksHistory;

    protected $fillable = [
        'orcamento_id',
        'nf',
        'status',
        'last_user_id',
    ];

    public function orcamento(){
        return $this->belongsTo(Orcamento::class);
    }

    public function anexos(){
        return $this->morphMany(Anexo::class, 'anexable');
    }
    
    public function contasReceber()
    {
        return $this->hasMany(ContasReceber::class);
    }
    
}
