<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoPonente extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'eventos_ponentes';
    public $timestamps = false;

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'event_id');
    }

    public function ponente()
    {
        return $this->belongsTo(Ponente::class, 'speaker_id');
    }
}
