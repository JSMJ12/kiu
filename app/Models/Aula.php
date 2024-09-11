<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'piso',
        'codigo',
        'paralelos_id',
    ];

    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'paralelos_id');
    }
    public function cohortes()
    {
        return $this->hasMany(Cohorte::class);
    }
}
