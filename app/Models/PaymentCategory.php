<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class PaymentCategory extends Model
{
    use GenerateUuid;
   protected $fillable = [
        'name',
        'description',
        'organisation_id'
   ];

   public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'uuid';


    public function paymentItem() {
        return $this->hasMany(PaymentItem::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }
}
