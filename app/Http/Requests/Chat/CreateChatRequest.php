<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatRequest extends FormRequest
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
            'user_hash_id' =>'required|exists:users,hash_id',
            'body' =>'required'
        ];
    }
    public function messages()
    {
        return [
            'user_hash_id.required' => 'O campo do destinatário é obrigatório.',
            'user_hash_id.exists' => 'O campo destinatário não existis.',
            'body.required' => 'O conteúdo da mensagem é obrigatório.',
            
        ];
    }
}
