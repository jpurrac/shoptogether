<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:100',
            'budget' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la lista es obligatorio.',
            'budget.integer' => 'El presupuesto debe ser un número entero.',
            'budget.min'     => 'El presupuesto no puede ser negativo.',
        ];
    }
}
