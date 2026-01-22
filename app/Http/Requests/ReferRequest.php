<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ReferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'origen_id_stockcenter' => 'required|integer|exists:stockcenters,id',
            'destiny_id_stockcenter' => 'required|integer|exists:stockcenters,id',
            'observation' => 'nullable|string|max:60',
            'date_up' => 'required|date_format:Y-m-d H:i',
        ];

        // Solo requerir date_ended para finalización
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['status'] = 'sometimes|string|max:1|in:I,E,F,C';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es requerido.',
            'exists' => 'El :attribute seleccionado no existe.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'in' => 'El campo :attribute debe ser uno de los valores: :values.',
            'date_format' => 'El campo :attribute debe tener el formato Y-m-d H:i.'
        ];
    }

    public function prepareForValidation(): void
    {
        // Convertir fechas si vienen con T (desde input datetime-local)
        if ($this->has('date_up')) {
            $this->merge(['date_up' => str_replace('T', ' ', $this->date_up)]);
        }

        if ($this->has('date_ended')) {
            $this->merge(['date_ended' => str_replace('T', ' ', $this->date_ended)]);
        }

        // Establecer usuario actual para nuevos registros
        if ($this->isMethod('post') && auth()->check()) {
            $this->merge(['id_user' => auth()->id()]);
        }
    }
}