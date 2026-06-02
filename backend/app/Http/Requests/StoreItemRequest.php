<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:150',
            'qty'   => 'nullable|integer|min:1|max:999',
            'price' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'qty.min'       => 'La cantidad mínima es 1.',
            'qty.max'       => 'La cantidad máxima es 999.',
            'price.min'     => 'El precio no puede ser negativo.',
        ];
    }
}
