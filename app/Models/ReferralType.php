<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralType extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referral_types';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'referral_type_id';

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

        static::deleting(function ($referralType) {
            if ($referralType->is_generated) {
                abort(403, 'You are not allowed to delete a generated referral type.');
            }
        });
    }

    /**
     * Get the referrals that belong to the referral type.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referral_type_id', 'referral_type_id');
    }
}
