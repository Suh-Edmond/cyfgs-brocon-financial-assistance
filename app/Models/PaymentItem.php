<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

/**
 * @method static find(mixed $payment_item_id)
 * @method static create(array $array)
 */
class PaymentItem extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'name',
        'amount',
        'compulsory',
        'payment_category_id',
        'description',
        'updated_by',
        'type',
        'frequency',
        'session_id',
        'reference'
    ];

    public function paymentCategory(){
        return $this->belongsTo(PaymentCategory::class);
    }

    public function userContribution() {
        return $this->hasMany(UserContribution::class);
    }

    public function activitySupports() {
        return $this->hasMany(ActivitySupport::class);
    }

    public function incomeActivities() {
        return $this->hasMany(IncomeActivity::class);
    }

    public function expenditureItems()
    {
        return $this->hasMany(ExpenditureItem::class);
    }

    public function session() {
        return $this->belongsTo(Session::class);
    }
}
