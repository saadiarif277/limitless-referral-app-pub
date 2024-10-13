<?php

namespace App\Http\Requests;

use App\Models\UserType;
use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('doctor.store');
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_type_id' => UserType::DOCTOR,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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

            // Access & Security
            'user_type_id' => 'required|integer|exists:App\Models\UserType,user_type_id',
            'medical_specialty_id' => 'required|integer|exists:App\Models\MedicalSpecialty,medical_specialty_id',
        ];
    }
}
