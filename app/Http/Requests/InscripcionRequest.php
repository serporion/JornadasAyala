<?php

namespace App\Http\Requests;

use App\Models\Evento;
use App\Models\User;
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
            'eventos' => 'required|array|min:1', // Al menos un evento debe seleccionarse
            'eventos.*' => 'exists:eventos,id', // Verificar que los IDs de eventos existan en la base de datos
        ];
    }

    /**
     * Método que personaliza la validación después de las reglas definidas.
     * Se verifica los radios dinámicos que he tenido que hacer dinámicos
     * (tipo_inscripcion_{evento_id}), para preguntar por uno de todos ellos
     * que se han elegido.
     */
    protected function prepareForValidation()
    {
        /*
        // Decodificar `detalle` y agregar dinámicamente los tipos de inscripción al request
        $detalle = json_decode($this->input('detalle', '[]'), true);

        if (is_array($detalle)) {
            foreach ($detalle as $eventoId => $item) {
                $this->merge([
                    "tipo_inscripcion_$eventoId" => $item['tipo_inscripcion'] ?? null,
                ]);
            }
        }
*/

        $detalle = json_decode($this->input('detalle'), true);

        if (!is_array($detalle)) {
            return;
        }

        foreach ($detalle as $eventoId => $item) {
            // Extraer y añadir dinámicamente los tipos de inscripción al request
            $this->merge([
                "tipo_inscripcion_$eventoId" => $item['tipo_inscripcion'] ?? null,
            ]);
        }



        // Normalizar `eventos` -> Si ya es un array, úsalo; si es string JSON, decodifícalo
        $eventos = $this->input('eventos', []);
        if (is_string($eventos)) {
            $eventos = json_decode($eventos, true); // Decodificar el JSON si es string
        }

        $this->merge([
            'eventos' => is_array($eventos) ? $eventos : [], // Asegurarse de que siempre sea un array
        ]);
    }

    public function withValidator($validator)
    {


        $validator->after(function ($validator) {
            // Obtener los eventos seleccionados del request
            $eventosSeleccionados = $this->input('eventos', []); //Lo envío con JSON. No me vale.


            // Validar si cada evento tiene plazas disponibles
            $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();

            foreach ($eventos as $evento) {
                if ($evento->cupo_maximo <= 0) {
                    $validator->errors()->add(
                        'eventos',
                        "No hay plazas disponibles para el evento: {$evento->nombre}."
                    );
                }
            }

            // Validar dinámicamente los tipos de inscripción para cada evento
            foreach ($eventosSeleccionados as $eventoId) {

                if (!$this->has("tipo_inscripcion_$eventoId")) {
                    continue; // Ignorar la validación si no existe el campo
                }

                $tipoInscripcion = $this->input("tipo_inscripcion_$eventoId");

                if (!$tipoInscripcion) {
                    $validator->errors()->add(
                        "tipo_inscripcion_$eventoId",
                        "Debe seleccionar el tipo de inscripción para el evento seleccionado."
                    );
                }
            }
        });
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
            'eventos.required' => 'Debe seleccionar al menos un evento.',
            'eventos.array' => 'Los eventos seleccionados deben ser un conjunto válido.',
            'eventos.min' => 'Debe seleccionar al menos un evento.',
            'eventos.*.exists' => 'Alguno de los eventos seleccionados no es válido.',
            'tipo_inscripcion.required' => 'El tipo de inscripción es obligatorio para cada evento seleccionado.',
            'tipo_inscripcion.string' => 'El tipo de inscripción debe ser válido.',
        ];
    }


}
