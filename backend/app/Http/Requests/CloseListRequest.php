<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseListRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'paid_by'        => 'required|integer|exists:users,id',
            'total_real'     => 'required|integer|min:0',
            'payment_method' => 'required|in:debito,credito,efectivo,transferencia',
            'comment'        => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'paid_by.required'        => 'Debes indicar quién pagó.',
            'paid_by.exists'          => 'El usuario indicado no existe.',
            'total_real.required'     => 'El total real pagado es obligatorio.',
            'total_real.min'          => 'El total no puede ser negativo.',
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.in'       => 'Método de pago inválido.',
        ];
    }
}
