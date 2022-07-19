<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class UserSaving extends Model
{
    use GenerateUuid;
    protected $fillable = [
        'amount_deposited',
        'comment',
        'user_id',
        'approve'
    ];

    public $incrementing = false;
   public $keyType = 'string';
   public $primaryKey = 'uuid';


   public function user(){
    return $this->belongsTo(User::class);
   }
}
