<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventoPonente extends Pivot
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'eventos_ponentes';
    public $timestamps = false;

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'event_id');
    }

    public function ponentes()
    {
        return $this->belongsToMany(Ponente::class, 'eventos_ponentes', 'event_id', 'speaker_id')
            ->using(EventoPonente::class);
    }
}
