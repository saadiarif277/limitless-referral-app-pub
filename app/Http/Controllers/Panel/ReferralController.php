<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\ProcedureResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\ReferralReasonResource;
use App\Http\Resources\ReferralStatusResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyReferralRequest;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Repositories\ReferralRepository;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Procedure;
use App\Models\Referral;
use App\Models\ReferralReason;
use App\Models\ReferralStatus;
use App\Models\State;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ReferralController extends Controller
{
    private ReferralRepository $referralRepository;

    /**
     * Create a new instance.
     */
    public function __construct(ReferralRepository $referralRepository) 
    {
        $this->referralRepository = $referralRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/referrals/referrals-list', [
            'referrals' => ReferralResource::collection($this->referralRepository->getItemsQuery($request)->paginate(20)),
            'referralStatuses' => ReferralStatusResource::collection(ReferralStatus::all()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $currentUser = auth()->user();

        return Inertia::render('panel/referrals/referrals-create-edit', [
            'documentCategories' => DocumentCategoryResource::collection(DocumentCategory::referralCreateDocuments($currentUser, $request->get('state_id', $currentUser->state_id))->get()),
            'patients' => UserResource::collection(User::referralPatients($currentUser, $request->get('state_id', $currentUser->state_id))->get()),
            'procedures' => ProcedureResource::collection(Procedure::all()),
            'recipients' => UserResource::collection(User::referralRecipients($currentUser, $request->get('state_id', $currentUser->state_id))->get()),
            'referralReasons' => ReferralReasonResource::collection(ReferralReason::all()),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReferralRequest $request)
    {
        $referral = $this->referralRepository->store($request);

        return to_route('panel.referrals.show', [
            'referral' => $referral->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Referral $referral): Response
    {
        $currentUser = auth()->user();

        $referral->load([
            'documents',
            'procedure',
            'patientUser.state',
            'recipientUser.state',
            'referralReasons',
        ]);

        return Inertia::render('panel/referrals/referrals-create-edit', [
            'documentCategories' => DocumentCategoryResource::collection(DocumentCategory::referralCreateDocuments($currentUser, $referral->state_id)->get()),
            'patients' => UserResource::collection(User::referralPatients($currentUser, $request->get('state_id', $referral->state_id))->get()),
            'procedures' => ProcedureResource::collection(Procedure::all()),
            'recipients' => UserResource::collection(User::referralRecipients($currentUser, $request->get('state_id', $referral->state_id))->get()),
            'referralReasons' => ReferralReasonResource::collection(ReferralReason::all()),
            'referral' => new ReferralResource($referral),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReferralRequest $request, Referral $referral)
    {
        $referral = $this->referralRepository->update($request, $referral);

        return to_route('panel.referrals.show', [
            'referral' => $referral->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyReferralRequest $request, Referral $referral)
    {
        $this->referralRepository->destroy($request);
        return to_route('panel.referrals.index');
    }

    /**
     * Generate and download summary document.
     */
    public function getSummaryDocument(Referral $referral)
    {
        $documentType = \App\Models\DocumentType::findOrFail(\App\Models\DocumentType::REFERRAL_SUMMARY);

        // Render your Blade view into HTML
        $html = view('pdf.referral-summary', ['referral' => $referral])->render();

        // Load the HTML into DOMPDF
        $pdf = PDF::loadHTML($html);

        // Generate a file name for the PDF
        $fileName = "";
        $fileName .= str()->slug($referral->patientUser->name);
        $fileName .= '-'.str()->slug($documentType->name);
        $fileName .= '-'.now()->format('Y-m-d-H:i:s');
        $fileName .= '-REF#'.$referral->getKey();
        $fileName .= '.pdf';

        return $pdf->download($fileName);
    }
}
