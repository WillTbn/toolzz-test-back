<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ];
    }
    public function messages()
    {
        return [
            'text.required' => 'O conteudo é obrigatório.',
            'receiver_id.required' => 'O campo do destinatário é obrigatório.',
            'receiver_id.exists' => 'O campo do destinatário não encontrado.',
        ];
    }
}
