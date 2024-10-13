<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use Impersonate;

    const SYSTEM = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'birthdate',
        'email',
        'password',
        'address_line_1',
        'address_line_2',
        'city',
        'zip_code',
        'timezone',
        'gender',
        'phone_number',
        'state_id',
        'height',
        'weight',
        'medical_specialty_id',
        'user_type_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Scope a query to only include referral patients.
     */
    public function scopeReferralPatients(Builder $query, User $user, $stateId): void
    {
        $query
            ->when(!$user->can('user.list'), function ($altQuery) use ($user) {
                $altQuery
                    ->whereHas('organizations', function ($subQuery) use ($user) {
                        $subQuery->whereIn('organizations.organization_id', $user->organizations()->pluck('organizations.organization_id')->toArray());
                    });
            })
            ->whereIn('users.user_type_id', [UserType::PATIENT])
            ->where('users.state_id', $stateId)
            ->orderBy('name');
    }    

    /**
     * Scope a query to only include referral recipients.
     */
    public function scopeReferralRecipients(Builder $query, User $user, $stateId): void
    {
        $query
            ->when(!$user->can('user.list'), function ($altQuery) use ($user) {
                $altQuery
                    ->whereHas('organizations', function ($subQuery) use ($user) {
                        $subQuery->whereIn('organizations.organization_id', $user->organizations()->pluck('organizations.organization_id')->toArray());
                    });
            })
            ->whereIn('users.user_type_id', [UserType::ATTORNEY, UserType::DOCTOR])
            ->where('users.state_id', $stateId)
            ->orderBy('name');
    }

    /**
     * Determine if the user can impersonate.
     */
    public function canImpersonate(): bool
    {
        return in_array($this->user_type_id, [
            UserType::SYSTEM,
            UserType::ADMINISTRATOR,
        ]);
    }

    /**
     * Determine if the user can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        return !in_array($this->user_type_id, [
            UserType::SYSTEM,
            UserType::ADMINISTRATOR,
        ]);
    }

    /**
     * Get the user's document types through the roles.
     */
    public function getDocumentTypes()
    {
        // First, collect all permissions related to document types from the user's roles.
        $documentTypeIds = collect($this->roles->flatMap(function ($role) {
            return $role->permissions;
        }))->filter(function ($permission) {
            // Filter to only include permissions like 'document-type.{id}.show'
            return preg_match('/^document-type\.(\d+)\.show$/', $permission->name);
        })->map(function ($permission) {
            // Extract just the ID part of the permission name
            preg_match('/^document-type\.(\d+)\.show$/', $permission->name, $matches);
            return (int) $matches[1];
        })->unique();
    
        // Fetch the actual DocumentType models based on extracted IDs
        return collect(\App\Models\DocumentType::whereIn('document_type_id', $documentTypeIds->toArray())->get()->toArray());
    }

    /**
     * Get the primary organization of the user.
     */
    public function getPrimaryOrganizationAttribute()
    {
        return $this->organizations()
            ->wherePivot('is_primary', true)
            ->first();
    }

    /**
     * Get the medical specialty that the user belongs to.
     */
    public function medicalSpecialty()
    {
        return $this->belongsTo(MedicalSpecialty::class, 'medical_specialty_id', 'medical_specialty_id');
    }

    /**
     * Get the organizations that belong to the user.
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'pivot_organizations_users', 'user_id', 'organization_id')
            ->using(Pivot\OrganizationUser::class)
            ->withPivot([
                'organization_role_id',
                'is_primary',
            ]);
    }

    /**
     * Get the referrals that belong to the user.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'patient_user_id', 'user_id');
    }

    /**
     * Get the referrals that the user belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    /**
     * Get the user type that the user belongs to.
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id', 'user_type_id');
    }
}
