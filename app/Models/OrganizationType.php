<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationType extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;
    
    const SYSTEM = 1;
    const LEGAL = 2;
    const FUNDING = 3;
    const CLINICS_MEDICAL_OFFICES = 4;
    const AMBULATORY_SURGICAL_CENTER = 5;
    const HOSPITAL = 6;
    const IMAGING_RADIOLOGY_CENTER = 7;
    const MEDICAL_LABORATORY = 8;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization_types';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'organization_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
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
     * Get the organizations that belong to the organization type.
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'organization_type_id', 'organization_type_id');
    }
}
