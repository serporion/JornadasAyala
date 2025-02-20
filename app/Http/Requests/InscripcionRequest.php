<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InscripcionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        // return backpack_auth()->check(); Parece que solo se usa para cuesiones de panel de administración.
        return auth()->check() && auth()->user()->role('user');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    /*
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'tipo_inscripcion_id' => 'required|exists:tipos_inscripcion,id',
        ];
    }
    */

    public function rules()
    {
        return [
            'eventos' => 'required|array|min:1', // Validar que se envíe un array con al menos un evento
            'tipo_inscripcion' => 'required|string', // Validar que tipo_inscripcion sea obligatorio
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
        /* //Si necesito id usuario e id tipo inscripcion
        return [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario seleccionado no existe',
            'tipo_inscripcion_id.required' => 'El tipo de inscripción es obligatorio',
            'tipo_inscripcion_id.exists' => 'El tipo de inscripción seleccionado no existe'
        ];

        */

        return [
            'eventos.required' => 'Debe seleccionar al menos un evento.',
            'eventos.array' => 'Los eventos seleccionados deben ser un conjunto válido.',
            'eventos.min' => 'Debe seleccionar al menos un evento.',
            'tipo_inscripcion.required' => 'El tipo de inscripción es obligatorio.',
            'tipo_inscripcion.string' => 'El tipo de inscripción debe ser válido.',
        ];
    }


}
