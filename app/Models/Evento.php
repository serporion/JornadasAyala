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

    public function tienePlazasDisponibles(): bool
    {
        return $this->cupo_maximo > 0;
    }

    public function restarPlaza(): void
    {
        // Verificar si aún hay plazas disponibles antes de restar
        if (!$this->tienePlazasDisponibles()) {
            throw new \Exception('No hay plazas disponibles para este evento: ' . $this->nombre);
        }

        $this->cupo_maximo -= 1;
        $this->save();
    }

}
