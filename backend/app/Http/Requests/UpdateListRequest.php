<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'   => 'sometimes|required|string|max:100',
            'budget' => 'sometimes|nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre no puede estar vacío.',
            'budget.integer' => 'El presupuesto debe ser un número entero.',
            'budget.min'     => 'El presupuesto no puede ser negativo.',
        ];
    }
}
