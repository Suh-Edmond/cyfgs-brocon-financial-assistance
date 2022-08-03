<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class UserSaving extends Model
{

    protected $fillable = [
        'amount_deposited',
        'comment',
        'user_id',
    ];



   public function user(){
    return $this->belongsTo(User::class);
   }
}
