<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Models\UserType;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('user.store');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // General Information
            'name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'birthdate' => 'nullable|date_format:Y-m-d',

            // Location Information
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'state_id' => 'required|integer|exists:App\Models\State,state_id',
            'zip_code' => 'nullable|string',
            'timezone' => 'nullable|string',

            // Personal Health Information
            'gender' => 'nullable|string|in:male,female,other',
            'height' => 'nullable|string',
            'weight' => 'nullable|string',

            // Access & Security
            'password' => 'required|confirmed|min:6',
            'role_names' => 'nullable|array',
            'medical_specialty_id' => 'nullable|integer|exists:App\Models\MedicalSpecialty,medical_specialty_id',
            'user_type_id' => 'required|integer|exists:App\Models\UserType,user_type_id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'integer|exists:App\Models\Organization,organization_id',
        ];

        // Check if the user has the 'Patient' user type.
        if ($this->user_type_id == UserType::PATIENT) {
            $rules['height'] = 'required|string';
            $rules['weight'] = 'required|string';
        }

        return $rules;
    }
}
