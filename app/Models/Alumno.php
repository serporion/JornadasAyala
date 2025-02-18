<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'alumnos';

    protected $guarded = ['id'];

    protected $fillable = [
        'nombre',
        'email',
        'dni'
    ];
}
