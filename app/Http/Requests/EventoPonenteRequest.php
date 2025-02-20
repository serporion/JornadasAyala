<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventoPonenteRequest extends FormRequest
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
            'event_id' => 'required|exists:eventos,id',
            'speaker_id' => 'required|exists:ponentes,id'
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
            'event_id.required' => 'El evento es obligatorio',
            'event_id.exists' => 'El evento seleccionado no existe',
            'speaker_id.required' => 'El ponente es obligatorio',
            'speaker_id.exists' => 'El ponente seleccionado no existe'
        ];
    }

}
