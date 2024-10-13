<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use DB;

class UserRepository
{
    /**
     * Get the query for getting all the items.
     */
    public function getItemsQuery(Request $request = null): Builder
    {
        $currentUser = auth()->user();

        return User::query()
            /**
             * Filter by the search query, if available.
             */
            ->when(!blank($request) && $request->filled('searchQuery'), function ($query) use ($request) {
                $searchTerm = strtolower($request->get('searchQuery'));
                $query
                    ->orWhereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
            })
            
            ->orderBy('name');
    }

    /**
     * Get the query for getting all the items that are patients.
     */
    public function getPatientsQuery(Request $request = null): Builder
    {
        $currentUser = auth()->user();

        return $this->getItemsQuery($request)
            ->where('user_type_id', UserType::PATIENT)

            /**
             * Filter by permission, if available.
             */
            ->when(!$currentUser->can('patient.list'), function ($permissionQuery) use ($currentUser) {
                $permissionQuery
                    ->whereHas('referrals', function ($subQuery) use ($currentUser) {
                        $subQuery
                            /**
                             * Get the users who have referrals where the patient is
                             * the current user.
                             */
                            ->where('referrals.patient_user_id', $currentUser->getKey())
        
                            /**
                             * Get the users who have referrals where the doctor is the
                             * current user.
                             */
                            ->orWhere('referrals.doctor_user_id', $currentUser->getKey())
        
                            /**
                             * Get the users who have referrals where the clinic is within the
                             * current user's assigned clinics.
                             */
                            ->orWhereIn('referrals.clinic_id', $currentUser->clinics->pluck('clinic_id')->toArray())
        
                            /**
                             * Get the users who have referrals where the attorney is the current
                             * user or the attorney's law firm is the same as the current user.
                             */
                            ->orWhereHas('attorneyUser', function ($subQuery) use ($currentUser) {
                                $subQuery
                                    ->where('referrals.attorney_user_id', $currentUser->getKey())
                                    ->orWhereHas('lawFirm', function($altQuery) use ($currentUser) {
                                        $altQuery
                                            ->where('law_firms.law_firm_id', $currentUser->law_firm_id);
                                    });
                            });
                    });
            });
    }

    /**
     * Get the query for getting all the items that are doctors.
     */
    public function getDoctorsQuery(Request $request = null): Builder
    {
        return $this->getItemsQuery($request)
            ->where('users.user_type_id', UserType::DOCTOR);
    }

    /**
     * Get the query for getting all the items that are patients.
     */
    public function getAttorneysQuery(Request $request = null): Builder
    {
        return $this->getItemsQuery($request)
            ->where('users.user_type_id', UserType::ATTORNEY);
    }

    /**
     * Create a new instance of the resource.
     */
    public function store(FormRequest $request): User
    {
        DB::beginTransaction();

        try {
            $payload = [];
            
            if ($request->filled('user_type_id')) {
                $payload['user_type_id'] = $request->validated()['user_type_id'];
            }

            if ($request->filled('timezone')) {
                $payload['timezone'] = $request->get('timezone');
            }

            $user = User::create(array_merge($payload, [
                // General Information
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone_number' => $request->get('phone_number'),
                'birthdate' => $request->get('birthdate'),
    
                // Location Information
                'address_line_1' => $request->get('address_line_1'),
                'address_line_2' => $request->get('address_line_2'),
                'city' => $request->get('city'),
                'state_id' => $request->get('state_id'),
                'zip_code' => $request->get('zip_code'),
                
                // Personal Health Information
                'gender' => $request->get('gender'),
                'height' => $request->get('height'),
                'weight' => $request->get('weight'),
    
                // Access & Security
                'user_type_id' => $request->validated()['user_type_id'],
                'password' => bcrypt($request->get('password')),
                'medical_specialty_id' => $request->get('medical_specialty_id'),
            ]));
    
            $user->syncRoles($request->get('role_names'));
            $user->organizations()->sync($request->get('organization_ids'));

            DB::commit();
            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an instance of the resource.
     */
    public function update(FormRequest $request, User $user): User
    {
        DB::beginTransaction();

        try {
            $payload = [];

            if ($request->filled('password')) {
                $payload['password'] = $request->get('password');
            }

            if ($request->filled('user_type_id')) {
                $payload['user_type_id'] = $request->get('user_type_id');
            }

            if ($request->filled('timezone')) {
                $payload['timezone'] = $request->get('timezone');
            }

            $user->update(array_merge($payload, [
                // General Information
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone_number' => $request->get('phone_number'),
                'birthdate' => $request->get('birthdate'),

                // Location Information
                'address_line_1' => $request->get('address_line_1'),
                'address_line_2' => $request->get('address_line_2'),
                'city' => $request->get('city'),
                'state_id' => $request->get('state_id'),
                'zip_code' => $request->get('zip_code'),
                
                // Personal Health Information
                'gender' => $request->get('gender'),
                'height' => $request->get('height'),
                'weight' => $request->get('weight'),

                // Access & Security
                'medical_specialty_id' => $request->get('medical_specialty_id'),
            ]));

            $user->syncRoles($request->get('role_names'));
            $user->organizations()->sync($request->get('organization_ids'));

            DB::commit();
            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an instance of the resource.
     */
    public function destroy(FormRequest $request, User $user): boolean
    {
        return (bool) $user->delete();
    }

    /**
     * Get the timezones available for the users to choose.
     */
    public function getTimezoneOptions(): Collection
    {
        $timezones = \DateTimeZone::listIdentifiers();

        $timezoneOptions = [];

        foreach ($timezones as $timezone) {
            // Check if the timezone starts with "America/" (for US time zones) or "Asia/" (for Asian time zones)
            if (strpos($timezone, 'America/') === 0 || strpos($timezone, 'Asia/') === 0 || $timezone === 'GMT') {
                $dateTime = new \DateTime(null, new \DateTimeZone($timezone));
                $offset = $dateTime->format('P'); // Get the GMT offset in ISO 8601 format (+HH:MM)
                $timezoneOptions[$timezone] = "$timezone (GMT$offset)";
            }
        }

        // Sort the timezone options array based on the GMT offset
        uksort($timezoneOptions, function($a, $b) {
            $aOffset = (new \DateTime(null, new \DateTimeZone($a)))->getOffset();
            $bOffset = (new \DateTime(null, new \DateTimeZone($b)))->getOffset();
            return $aOffset - $bOffset;
        });

        return collect($timezoneOptions);
    }
}
