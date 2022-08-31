<?php
namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasRoles, Notifiable, HasApiTokens;
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'gender',
        'address',
        'occupation',
        'organisation_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected  $guard_name = "api";

    public function hasUserSaving(){
        return $this->hasMany(UserSaving::class);
    }

    public function userPayment() {
        return $this->hasMany(UserPayment::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }




}
