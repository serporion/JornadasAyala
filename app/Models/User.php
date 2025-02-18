<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use CrudTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'confirmado',
        'token',
        'tipo_inscripcion',
        'es_alumno',
    ];

    protected $table = 'users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed','confirmado' => 'boolean',
        'es_alumno' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function eventos()
    {
        return $this->belongsToMany(Evento::class, 'asistentes_eventos', 'user_id', 'event_id');
    }

    public function inscripcion()
    {
        return $this->belongsTo(TipoInscripcion::class, 'tipo_inscripcion');
    }

    /**
     * Obtener el correo electrónico del usuario.
     */
    public function getEmailAttribute()
    {
        return $this->attributes['email'];
    }

}

