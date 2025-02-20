<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PonenteRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'fotografia' => 'nullable|string|max:255',
            'area_experiencia' => 'nullable|string|max:255',
            'red_social' => 'nullable|string|max:255',
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
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.string' => 'El nombre debe ser texto',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'fotografia.max' => 'La ruta de la fotografía no puede superar los 255 caracteres',
            'area_experiencia.max' => 'El área de experiencia no puede superar los 255 caracteres',
            'red_social.max' => 'La red social no puede superar los 255 caracteres'
        ];
    }

}
