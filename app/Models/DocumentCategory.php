<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    const FINANCIAL = 1;
    const MEDICAL   = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document_categories';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'document_category_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The "boot" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('orderByName', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
    }

    /**
     * Scope a query to only include referral create documents.
     */
    public function scopeReferralCreateDocuments(Builder $query, User $user, $stateId): void
    {
        $userDocumentTypes = $user->getDocumentTypes();

        $query
            ->with('documentTypes', function ($query) use ($stateId, $userDocumentTypes) {
                $state = State::find($stateId);
                $documentTypeIds = $state
                    ? $state->documentTypes->pluck('document_type_id')->toArray()
                    : [];

                $secondaryDocumentTypeIds = $userDocumentTypes
                    ->pluck('document_type_id')
                    ->toArray();

                $query
                    ->whereIn('document_types.document_type_id', array_intersect($documentTypeIds, $secondaryDocumentTypeIds))
                    ->where('document_types.is_generated', false);
            })
            ->whereHas('documentTypes')
            ->orderBy('name');
    }        

    /**
     * Get the documents that belong to the document category.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'document_category_id', 'document_category_id');
    }

    /**
     * Get the document types that belong to the document category.
     */
    public function documentTypes()
    {
        return $this->hasMany(DocumentType::class, 'document_category_id', 'document_category_id');
    }
}
