<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'eventos';

    protected $guarded = ['id'];

    protected $fillable = [
        'tipo',
        'nombre',
        'descripcion',
        'fecha',
        'hora_inicio',
        'duracion',
        'lugar',
        'cupo_maximo',
    ];

    public function ponentes()
    {
        return $this->belongsToMany(Ponente::class, 'eventos_ponentes', 'event_id', 'speaker_id');
    }

    public function asistentes()
    {
        return $this->belongsToMany(User::class, 'asistentes_eventos', 'event_id', 'user_id');
    }

    public function getInscritosAttribute()
    {
        return $this->inscripciones()->count();
    }

    public function inscripciones()
    {
        //return $this->hasManyThrough(Inscripcion::class, InscripcionEvento::class);
        return $this->belongsToMany(Inscripcion::class, 'inscripcion_eventos', 'evento_id', 'inscripcion_id')
            ->withTimestamps();
    }

}
