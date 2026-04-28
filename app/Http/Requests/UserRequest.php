<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                Password::min(8),
                'confirmed',
            ],
            'is_admin' => ['boolean'],
        ];
    }
}
