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
            'user_two_id' =>'required|exists:users,id',
            'body' =>'required'
        ];
    }
    public function messages()
    {
        return [
            'user_two_id.required' => 'O campo do destinatário é obrigatório.',
            'user_two_id.exists' => 'O campo destinatário não existis.',
            'body.required' => 'O campo Category é obrigatório.',
        ];
    }
}
