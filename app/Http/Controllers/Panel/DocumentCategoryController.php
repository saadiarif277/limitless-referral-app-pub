<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Requests\DestroyDocumentCategoryRequest;
use App\Http\Requests\StoreDocumentCategoryRequest;
use App\Http\Requests\UpdateDocumentCategoryRequest;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/document-categories/document-categories-list', [
            'documentCategories' => DocumentCategoryResource::collection(
                DocumentCategory::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->paginate(20)
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/document-categories/document-categories-create-edit', [
            // ...
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentCategoryRequest $request)
    {
        $documentCategory = DocumentCategory::create($request->safe()->only([
            'name',
            'description',
        ]));

        return to_route('panel.document-categories.show', [
            'document_category' => $documentCategory->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, DocumentCategory $documentCategory): Response
    {
        return Inertia::render('panel/document-categories/document-categories-create-edit', [
            'documentCategory' => new DocumentCategoryResource($documentCategory),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentCategory $documentCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentCategoryRequest $request, DocumentCategory $documentCategory)
    {
        $documentCategory->update($request->safe()->only([
            'name',
            'description',
        ]));

        return to_route('panel.document-categories.show', [
            'document_category' => $documentCategory->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyDocumentCategoryRequest $request, DocumentCategory $documentCategory)
    {
        $documentCategory->delete();
        return to_route('panel.document-categories.index');
    }
}
