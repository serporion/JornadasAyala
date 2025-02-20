<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'user_id',
        'tipo_inscripcion_id',
        'fecha_inscripcion',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tipoInscripcion()
    {
        return $this->belongsTo(TipoInscripcion::class);
    }


}
