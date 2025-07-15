<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasPermission('manage_roles');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255|unique:roles,name',
            'description'     => 'nullable|string',
            'menu_ids'        => 'array',
            'menu_ids.*'      => 'exists:menus,id',
            'permission_ids'  => 'array',
            'permission_ids.*'=> 'exists:permissions,id',
        ];
    }
}
