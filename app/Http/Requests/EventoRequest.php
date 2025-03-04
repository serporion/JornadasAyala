<?php

namespace App\Http\Requests;

use App\Models\Evento;
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
        //return auth()->check() && auth()->user()->role('user');
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
            'hora_inicio' => 'required|date_format:H:i:s',
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
            'eventos' => 'required|array',
            'eventos.*' => 'exists:eventos,id',
        ];
    }

    /*
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            //$eventosSeleccionados = $this->input('eventos', []);
            $eventosSeleccionados = json_decode($this->input('eventos', '[]'), true);
            $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();

            foreach ($eventos as $evento) {
                if ($evento->plazas_disponibles <= 0) {
                    $validator->errors()->add(
                        "evento_{$evento->id}",
                        "No hay plazas disponibles para el evento: {$evento->nombre}"
                    );
                }
            }
        });
    }
    */


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
