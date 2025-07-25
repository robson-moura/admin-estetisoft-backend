<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id'      => 'required|exists:clients,id',
            'user_id'        => 'required|exists:users,id',
            'date'           => 'required|date',
            'time'           => 'nullable',
            'service_id'     => 'required|exists:services,id',
            'products_ids'   => 'nullable|array',
            'products_ids.*' => 'exists:products,id',
            'notes'          => 'nullable|string',
            'before_photo'   => 'nullable',
            'after_photo'    => 'nullable',
            'price'          => 'nullable|numeric',
            'payment_method' => 'nullable|string|max:50',
            'plan'           => 'nullable|string|max:255',
            'signature'      => 'nullable|string',
            'status'         => 'required|in:scheduled,completed,canceled,in_progress,absent',
        ];
    }

    public function messages()
    {
        return [
            'client_id.required'      => 'O campo cliente é obrigatório.',
            'client_id.exists'        => 'O cliente selecionado é inválido.',
            'user_id.required'        => 'O campo profissional é obrigatório.',
            'user_id.exists'          => 'O profissional selecionado é inválido.',
            'date.required'           => 'O campo data é obrigatório.',
            'date.date'               => 'O campo data deve ser uma data válida.',
            'service_id.required'     => 'O campo serviço é obrigatório.',
            'service_id.exists'       => 'O serviço selecionado é inválido.',
            'products_ids.array'      => 'Os produtos devem ser enviados em formato de lista.',
            'products_ids.*.exists'   => 'Um ou mais produtos selecionados são inválidos.',
            'notes.string'            => 'O campo observações deve ser um texto.',
            'price.numeric'           => 'O campo valor deve ser numérico.',
            'payment_method.string'   => 'O campo forma de pagamento deve ser um texto.',
            'payment_method.max'      => 'O campo forma de pagamento deve ter no máximo 50 caracteres.',
            'plan.string'             => 'O campo plano deve ser um texto.',
            'plan.max'                => 'O campo plano deve ter no máximo 255 caracteres.',
            'signature.string'        => 'O campo assinatura deve ser um texto.',
            'status.required'         => 'O campo status é obrigatório.',
            'status.in'               => 'O status selecionado é inválido.',
        ];
    }
}