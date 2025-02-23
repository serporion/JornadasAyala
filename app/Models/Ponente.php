<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ponente extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'ponentes';

    protected $guarded = ['id'];

    protected $fillable = [
        'nombre',
        'fotografia',
        'area_experiencia',
        'red_social',
    ];


    public function eventos()
    {
        //Muchos a muchos. No lo necesito.
        //return $this->belongsToMany(Evento::class, 'eventos_ponentes', 'speaker_id', 'event_id');
        return $this->hasMany(Evento::class); // Asume que 'eventos' está relacionado como hasMany.
    }
}
