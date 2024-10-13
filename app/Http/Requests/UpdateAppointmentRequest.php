<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('appointment.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'referral_id' => 'required|integer|exists:App\Models\Referral,referral_id',
            'description' => 'nullable|string',
            'appointment_type_id' => 'required|integer|exists:App\Models\AppointmentType,appointment_type_id',
            'organization_id' => 'required|integer|exists:App\Models\Organization,organization_id',
            'patient_user_id' => 'required|integer|exists:App\Models\User,user_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'appointment_type_id.required' => 'The appointment type field is required.',
            'organization_id.required' => 'The organization field is required.',
            'patient_user_id.required' => 'The patient field is required.',
            'referral_id.required' => 'The referral field is required.',
        ];
    }
}
