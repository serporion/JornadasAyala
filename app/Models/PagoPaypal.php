<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoPaypal extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',
        'transaccionPaypal',
        'total',
        'eventos',
    ];

    /**
     * Indica que el campo "eventos" debe tratarse como JSON.
     */
    protected $casts = [
        'eventos' => 'array',
    ];

    /**
     * Relación: Una inscripción pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
