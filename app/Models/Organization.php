<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organizations';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'organization_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'description',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state_id',
        'zip_code',
        'organization_type_id',
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
     * Get the organization type that the organization belongs to.
     */
    public function organizationType()
    {
        return $this->belongsTo(OrganizationType::class, 'organization_type_id', 'organization_type_id');
    }

    /**
     * Get the state that the clinic belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    /**
     * Get the users that belong to the organization.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'pivot_organizations_users', 'organization_id', 'user_id')
            ->using(Pivot\OrganizationUser::class)
            ->withPivot([
                'organization_role_id',
                'is_primary',
            ]);
    }
}
