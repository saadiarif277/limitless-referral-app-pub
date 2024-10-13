<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\DocumentType;
use App\Models\LawFirm;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserType;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use DB;

class ReferralRepository
{
    /**
     * Get all the referrals that the authenticated user can access.
     */
    public function getItemsQuery(Request $request = null): Builder
    {
        $user = auth()->user();

        return Referral::query()
            ->with([
                'appointment.organization.state',
                'patientUser',
                'recipientUser',
                'referralStatus',
                'sourceUser',
            ])

            /**
             * Filter by the search query, if available.
             */
            ->when(!blank($request) && $request->filled('searchQuery'), function ($query) use ($request) {
                $searchTerm = strtolower($request->get('searchQuery'));
                $query
                    ->leftJoin('users as patient_users', 'referrals.patient_user_id', '=', 'patient_users.user_id')
                    ->leftJoin('users as recipient_users', 'referrals.recipient_user_id', '=', 'recipient_users.user_id')
                    ->leftJoin('users as source_users', 'referrals.source_user_id', '=', 'source_users.user_id')
                    ->where(function ($subQuery) use ($searchTerm) {
                        $subQuery
                            ->orWhereRaw('LOWER(patient_users.name) LIKE ?', ["%{$searchTerm}%"])
                            ->orWhereRaw('LOWER(patient_users.email) LIKE ?', ["%{$searchTerm}%"])
                            ->orWhereRaw('LOWER(recipient_users.name) LIKE ?', ["%{$searchTerm}%"])
                            ->orWhereRaw('LOWER(recipient_users.email) LIKE ?', ["%{$searchTerm}%"])
                            ->orWhereRaw('LOWER(source_users.name) LIKE ?', ["%{$searchTerm}%"])
                            ->orWhereRaw('LOWER(source_users.email) LIKE ?', ["%{$searchTerm}%"]);
                });
            })

            /**
             * Filter results by the selected referral status ID, if available.
             */
            ->when(!blank($request) && $request->filled('referralStatusId'), function ($query) use ($request) {
                $query->where('referrals.referral_status_id', $request->get('referralStatusId'));
            })

            /**
             * Filter by permission, if available.
             */
            ->when(!$user->can('referral.list'), function ($mainQuery) use ($user) {
                $organizationIds = $user
                    ->organizations()
                    ->pluck('organizations.organization_id')
                    ->toArray();

                $mainQuery->where(function ($query) use ($organizationIds, $user) {

                    $query->orWhere('patient_user_id', $user->getKey());
                    $query->orWhere('recipient_user_id', $user->getKey());
                    $query->orWhere('source_user_id', $user->getKey());

                    $query->orWhereHas('patientUser', function ($subQuery) use ($organizationIds) {
                        $subQuery->whereHas('organizations', function ($altQuery) use ($organizationIds) {
                            $altQuery->whereIn('organizations.organization_id', $organizationIds);
                        });
                    });
    
                    $query->orWhereHas('recipientUser', function ($subQuery) use ($organizationIds) {
                        $subQuery->whereHas('organizations', function ($altQuery) use ($organizationIds) {
                            $altQuery->whereIn('organizations.organization_id', $organizationIds);
                        });
                    });
    
                    $query->orWhereHas('sourceUser', function ($subQuery) use ($organizationIds) {
                        $subQuery->whereHas('organizations', function ($altQuery) use ($organizationIds) {
                            $altQuery->whereIn('organizations.organization_id', $organizationIds);
                        });
                    });

                });
            })
            ->orderBy('referral_date', 'desc')
            ->orderBy('referrals.referral_id', 'desc');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReferralRequest $request): Referral
    {
        DB::beginTransaction();

        try {
            /**
             * Prepare the payload for the referral.
             */
            $payload = $request->safe()->only([
                'notes',
                'patient_user_id',
                'procedure_id',
                'recipient_user_id',
                'referral_date',
                'referral_status_id',
                'referral_type_id',
                'source_user_id',
                'state_id',
            ]);

            /**
             * Create the referral.
             */
            $referral = Referral::create($payload);

            /**
             * Create Documents.
             */
            $documents = $request->validated()['documents'];
            $this->createDocuments($documents, $referral);

            /**
             * Sync the referral reasons with the referral.
             */
            $referralReasonIds = $request->validated()['referral_reason_ids'];
            $referral->referralReasons()->sync($referralReasonIds);
        
            DB::commit();
            return $referral->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an existing instance of the resource.
     */
    public function update(UpdateReferralRequest $request, Referral $referral): Referral
    {
        DB::beginTransaction();

        try {
            /**
             * Prepare the payload for the referral.
             */
            $payload = $request->safe()->only([
                'notes',
                'patient_user_id',
                'procedure_id',
                'recipient_user_id',
                'referral_date',
                'referral_status_id',
                'referral_type_id',
                'source_user_id',
                'state_id',
            ]);

            /**
             * Update the referral.
             */
            $referral->update($payload);

            /**
             * Create Documents.
             */
            $documents = $request->validated()['documents'];
            $this->createDocuments($documents, $referral);

            /**
             * Sync the referral reasons with the referral.
             */
            $referralReasonIds = $request->validated()['referral_reason_ids'];
            $referral->referralReasons()->sync($referralReasonIds);
        
            DB::commit();
            return $referral->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Create the documents related to the referral.
     */
    public function createDocuments(array $documents = [], Referral $referral): void
    {
        foreach($documents as $documentTypeId => $document)
        {
            // Ensure that the document is an uploaded file.
            if (!method_exists($document, 'getClientOriginalExtension')) {
                continue;
            }

            // Get the document type to be used.
            $documentType = DocumentType::findOrFail($documentTypeId);

            // Generate a file name for the PDF
            $fileName = "";
            $fileName .= Str::slug($referral->patientUser->name);
            $fileName .= '-'.Str::slug($documentType->name);
            $fileName .= '-'.now()->format('Y-m-d-H:i:s');
            $fileName .= '-REF#'.$referral->getKey();
            $fileName .= ".{$document->getClientOriginalExtension()}";

            // Upload the file to the specified path and get the stored file path
            $path = $document->storeAs('public/uploads/documents', $fileName);

            // Find the current document, if there is one.
            $currentDocument = $referral
                ->documents()
                ->where('document_type_id', $documentTypeId)
                ->first();

            if ($currentDocument) {
                $currentDocument->update([
                    'name' => $fileName,
                    'path' => $path,
                ]);
            } else {
                $referral->documents()->create([
                    'document_type_id' => $documentTypeId,
                    'name' => $fileName,
                    'path' => $path,
                ]);
            }
        }
    }
}
