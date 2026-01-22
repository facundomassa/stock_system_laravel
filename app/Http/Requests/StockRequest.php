<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Determinar si es una actualización (PUT, PATCH)
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        if ($isUpdate) {
            // Solo validar quantity_alert en actualizaciones
            return [
                'quantity_alert' => 'nullable|integer|min:0',
            ];
        }

        // Validación para creación
        return [
            'id_stockcenter' => 'required|integer|exists:stockcenters,id',
            'id_article' => 'required|integer|exists:articles,id',
            'quantity' => 'required|integer|min:0',
            'quantity_alert' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es requerido.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe ser al menos :min.',
            'exists' => 'El :attribute seleccionado no existe.'
        ];
    }
}