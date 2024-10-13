<?php

namespace App\Http\Requests;

use App\Models\DocumentType;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('referral.store');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedDocumentTypes = rtrim(DocumentType::get()->reduce(function ($carry, $item) {
            return $carry . $item->getKey() . ',';
        }, 'array:'), ',');

        $rules = [
            'notes' =>                  'nullable|string',
            'procedure_id' =>           'required|integer|exists:App\Models\Procedure,procedure_id',
            'recipient_user_id' =>      'required|integer|exists:App\Models\User,user_id',
            'referral_date' =>          'required|date',
            'referral_reason_ids' =>    'required|array',
            'referral_reason_ids.*' =>  'integer|exists:App\Models\ReferralReason,referral_reason_id',
            'referral_status_id' =>     'required|integer|exists:App\Models\ReferralStatus,referral_status_id',
            'referral_type_id'   =>     'required|integer|exists:App\Models\ReferralType,referral_type_id',
            'source_user_id' =>         'required|integer|exists:App\Models\User,user_id',
            'state_id' =>               'required|integer|exists:App\Models\State,state_id',
            'documents'              => "nullable|{$allowedDocumentTypes}",
            'documents.*'            => 'file|max:51200',

            // Patient
            'patient_create' =>         'required|boolean',
            'patient_user_id' =>        'required_if:patient_create,false|nullable|integer|exists:App\Models\User,user_id',
            'patient_name' =>           'required_if:patient_create,true|nullable|string',
            'patient_email' =>          'required_if:patient_create,true|nullable|email',
            'patient_phone_number' =>   'nullable|string',
            'patient_height' =>         'nullable|string',
            'patient_weight' =>         'nullable|string',
            'patient_gender' =>         'nullable|string',
            'patient_birthdate' =>      'nullable|date',
            'patient_address_line_1' => 'nullable|string',
            'patient_address_line_2' => 'nullable|string',
            'patient_city' =>           'nullable|string',
            'patient_state_id' =>       'required_if:patient_create,true|nullable|integer|exists:App\Models\State,state_id',
            'patient_zip_code' =>       'nullable|string',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'patient_user_id.required_if' => 'Please choose a patient for the referral.',
            'recipient_user_id.required'  => 'Please choose a recipient for the referral.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Filter out null and empty string values from the 'documents' array
        $documents = array_filter($this->documents, function ($value) {
            return !is_null($value) && $value !== '';
        });

        $this->merge([
            'documents' => $documents,
            'referral_type_id' => 2, // OTHER
            'patient_user_id' => $this->getPatientUserId(),
            'source_user_id' => auth()->user()->getKey(),
        ]);
    }

    /**
     * Get the patient user.
     */
    private function getPatientUserId()
    {
        if (!$this->get('patient_create')) {
            return $this->get('patient_user_id');
        }

        $patientUser = User::firstOrCreate([
            'email' => $request->get('patient_email'),
        ], [
            'name' => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            'height' => $request->get('height'),
            'weight' => $request->get('weight'),
            'gender' => $request->get('gender'),
            'birthdate' => $request->get('birthdate'),
            'address_line_1' => $request->get('address_line_1'),
            'address_line_2' => $request->get('address_line_2'),
            'city' => $request->get('city'),
            'state_id' => $request->get('state_id'),
            'zip_code' => $request->get('zip_code'),
            'user_type_id' => UserType::PATIENT,
            'password' => bcrypt(str()->random(16)),
        ]);

        return $patientUser->getKey();
    }
}
