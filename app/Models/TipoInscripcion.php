<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TipoInscripcion extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'tipos_inscripcion';

    protected $guarded = ['id'];

    protected $fillable = [
        'nombre',
        'precio',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'tipo_inscripcion');
    }
}
