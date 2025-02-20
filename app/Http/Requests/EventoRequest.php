<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipo' => 'required|in:conferencia,taller',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'duracion' => 'required|integer|min:1|max:180',
            'lugar' => 'required|string|max:255',
            'cupo_maximo' => 'required|integer|min:1',
        ];
    }


    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
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
            'tipo.required' => 'El tipo de evento es obligatorio',
            'tipo.in' => 'El tipo debe ser conferencia o taller',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'El formato de fecha no es válido',
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
            'hora_inicio.date_format' => 'El formato de hora no es válido',
            'duracion.required' => 'La duración es obligatoria',
            'duracion.integer' => 'La duración debe ser un número entero',
            'lugar.required' => 'El lugar es obligatorio',
            'cupo_maximo.required' => 'El cupo máximo es obligatorio',
            'cupo_maximo.integer' => 'El cupo máximo debe ser un número entero'
        ];
    }

}
