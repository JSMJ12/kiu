<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulacion extends Model
{
    use HasFactory;
    protected $fillable = [
        'alumno_dni',
        'titulado',
        'tesis_path',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
