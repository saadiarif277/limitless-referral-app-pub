<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('role.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'permission_ids' => 'nullable|array',
            'document_category_ids' => 'nullable|array',
            'document_type_ids' => 'nullable|array',
            'document_type_permissions' => 'nullable|array',
            'document_type_permissions.*' => 'required|array',
            'document_type_permissions.*.can_show' => 'required|boolean',
            'document_type_permissions.*.can_store' => 'required|boolean',
            'document_type_permissions.*.can_update' => 'required|boolean',
            'document_type_permissions.*.can_delete' => 'required|boolean',
        ];
    }
}
