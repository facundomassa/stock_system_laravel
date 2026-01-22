<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Article;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:60',
            'unit' => 'required|string|max:1|in:U,u,K,k,M,m',
            'type' => 'nullable|string|max:30',
            'code' => 'nullable|string|max:16'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puede tener más de :max caracteres',
            'in' => 'La unidad debe ser U, K o M'
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('unit')) {
            $this->merge([
                'unit' => strtoupper($this->unit)
            ]);
        }
    }
}
