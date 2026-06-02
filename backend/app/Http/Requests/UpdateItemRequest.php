<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'  => 'sometimes|required|string|max:150',
            'qty'   => 'sometimes|integer|min:1|max:999',
            'price' => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre no puede estar vacío.',
            'qty.min'       => 'La cantidad mínima es 1.',
            'qty.max'       => 'La cantidad máxima es 999.',
            'price.min'     => 'El precio no puede ser negativo.',
        ];
    }
}
