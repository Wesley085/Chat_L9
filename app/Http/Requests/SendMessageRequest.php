<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'to'      => 'required|string|email|exists:users,email', // Agora valida como e-mail e verifica na tabela de usuários
            'title'   => 'nullable|string|max:50',
            'message' => 'nullable|string|max:500',
            'media'   => 'nullable|file|mimes:jpeg,png,jpg,mp4,avi|max:10240', // 10MB
        ];
    }
}
