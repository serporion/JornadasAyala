<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlumnoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:alumnos,email,' . $this->id,
            'dni' => 'required|string|max:20|unique:alumnos,dni,' . $this->id,
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.string' => 'El nombre debe ser texto',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'dni.required' => 'El DNI es obligatorio',
            'dni.string' => 'El DNI debe ser texto',
            'dni.max' => 'El DNI no puede superar los 20 caracteres',
            'dni.unique' => 'Este DNI ya está registrado'
        ];
    }

}
