<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    const DRAFT = 1;
    const PENDING = 2;
    const BOOKED = 3;
    const TEST_DONE = 4;
    const SIGNED = 5;
    const SUBMITTED = 6;
    const SETTLED = 7;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referral_statuses';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'referral_status_id';

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

        static::addGlobalScope('orderByOrder', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->orderBy('order', 'asc');
        });

        static::addGlobalScope('filterByStatusId', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->whereNotIn('referral_status_id', [ReferralStatus::DRAFT]);
        });
    }

    /**
     * Get the referrals that belong to the referral status.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referral_status_id', 'referral_status_id');
    }
}
