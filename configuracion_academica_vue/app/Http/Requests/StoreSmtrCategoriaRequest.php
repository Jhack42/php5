<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmtrCategoriaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        // Si deseas permitir que todos los usuarios puedan hacer esta solicitud
        return true;

        // Si quieres restringir la acción solo a usuarios autenticados
        // return auth()->check();

        // O si deseas roles específicos, como administrador
        // return auth()->user()->hasRole('admin');
    }

    /**
     * Obtiene las reglas de validación para la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Si estamos editando (es decir, cuando tenemos un ID), el título es opcional.
        $rules = [
            'v_titulo' => 'required|string|max:50', // Para la creación, el título es obligatorio
        ];

        // Si la solicitud es de actualización, hacemos que el título sea opcional.
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['v_titulo'] = 'nullable|string|max:50'; // Título es opcional para la actualización
        }

        return $rules;
    }

    /**
     * Obtiene los mensajes de error personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'v_titulo.required' => 'El campo Título es obligatorio.',
            'v_titulo.string' => 'El Título debe ser una cadena de texto.',
            'v_titulo.max' => 'El Título no puede tener más de 50 caracteres.',
        ];
    }
}
