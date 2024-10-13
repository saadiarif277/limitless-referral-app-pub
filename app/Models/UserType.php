<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use HasFactory;
    use SoftDeletes;

    const GUEST = 1;
    const SYSTEM = 2;
    const ADMINISTRATOR = 3;
    const ATTORNEY = 4;
    const DOCTOR = 5;
    const PATIENT = 6;
    const FINANCE = 7;
    const PROVIDER = 8;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_types';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_type_id';

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
     * Get the users that belong to the user type.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_type_id', 'user_type_id');
    }
}
