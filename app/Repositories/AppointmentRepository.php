<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use DB;

class AppointmentRepository
{
    /**
     * Get all the appointments that the authenticated user can access.
     */
    public function getItemsQuery($request = null): Builder
    {
        $currentUser = auth()->user();

        return Appointment::query()
            ->with([
                'appointmentType',
                'organization',
            ])

            /**
             * If present, filter by selected organization IDs.
             */
            ->when(!blank($request) && $request->filled('selected_organization_ids'), function ($query) use ($request) {
                $query->whereIn('organization_id', $request->get('selected_organization_ids'));
            })

            /**
             * Filter by permission, if available.
             */
            ->when(!$currentUser->can('appointment.list'), function ($query) use ($currentUser) {
                $query
                    ->whereIn('organization_id', $currentUser->organizations->pluck('organization_id')->toArray())
                    ->orWhere('patient_user_id', $currentUser->getKey());
            });
    }

    /**
     * Create a new instance of the resource.
     */
    public function store($request): Appointment
    {
        DB::beginTransaction();

        try {
            $payload = $request->safe()->only([
                'referral_id',
                'description',
                'appointment_type_id',
                'organization_id',
                'patient_user_id',
                'start_date',
                'end_date',
            ]);

            $appointment = Appointment::create(array_merge($payload, [
                'slug' => Str::slug($payload['name']),
            ]));

            if ($request->filled('referral_id')) {
                $referral = Referral::findOrFail($request->validated()['referral_id'])
                    ->update([
                        'appointment_id' => $appointment->getKey(),
                        'referral_status_id' => ReferralStatus::BOOKED,
                    ]);
            }

            DB::commit();
            return $appointment->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an instance of the resource.
     */
    public function update($request, Appointment $appointment): Appointment
    {
        DB::beginTransaction();

        try {
            $appointment->update($request->safe()->only([
                'description',
                'appointment_type_id',
                'organization_id',
                'patient_user_id',
                'start_date',
                'end_date',
            ]));

            DB::commit();
            return $appointment->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an instance of the resource.
     */
    public function destroy(DestroyAppointmentRequest $request, Appointment $appointment): boolean
    {
        return $appointment->delete();
    }
}
