<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(
            'super_admin',
            'administrador'
        ) ?? false;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique('users', 'email')
                    ->ignore($usuario?->id),
            ],

            'role_id' => [
                'required',
                'integer',

                Rule::exists('roles', 'id')->where(
                    fn ($query) => $query
                        ->where('activo', true)
                ),
            ],

            'activo' => [
                'required',
                'boolean',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }
}
