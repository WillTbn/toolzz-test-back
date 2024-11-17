<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class PhotoUploadRequest extends FormRequest
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
            'photo' =>'required|file|mimes:png,jpeg,jpg|max:59240'
        ];
    }
    public function messages()
    {
        return [
            'photo.mimes' => 'Formato não suportado, aceitamos no formato PNG,JPEG ou JPG.',
        ];
    }
}
