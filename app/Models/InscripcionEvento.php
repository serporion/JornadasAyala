<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscripcionEvento extends Model
{
    use HasFactory;

    // Si la tabla no sigue la convención de nombres (inscripcion_eventos), indícalo:
    protected $table = 'inscripcion_eventos';

    // Si no utilizas incrementos automáticos
    public $incrementing = true;

    // Relaciones (definir si necesitas acceso desde aquí)
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}
