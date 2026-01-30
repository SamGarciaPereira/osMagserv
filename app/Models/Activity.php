<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'description',
        'user_id',
        'event',
        'version',
        'properties',
        'subject_type',
        'subject_id',
        'description',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}