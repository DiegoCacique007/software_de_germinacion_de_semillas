<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where(fn($query) => $query->where('activo', true)->whereIn('clave', ['super_admin', 'encargado']))],
            'activo' => ['required', 'boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
