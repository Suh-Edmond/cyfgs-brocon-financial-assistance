<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\GenerateUuid;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable, GenerateUuid;



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

    public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'uuid';
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

    public function hasUserSaving(){
        return $this->hasMany(UserSaving::class);
    }

    public function userPayment() {
        return $this->hasMany(UserPayment::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }

    public function roles() {
        return $this->hasMany(Role::class);
    }
}
