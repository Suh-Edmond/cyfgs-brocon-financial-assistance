<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\GenerateUuid;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable;



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

    public function guardName(){
        return "api";
    }

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
