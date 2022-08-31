<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class PaymentCategory extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'name',
        'description',
        'organisation_id'
    ];

    public function paymentItem()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
