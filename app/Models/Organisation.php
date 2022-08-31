<?php

namespace App\Models;
use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'name',
        'description',
        'telephone',
        'email',
        'address',
        'salutation',
        'region'
    ];

    public function expenditureCategory() {
        return $this->hasMany(ExpenditureCategory::class);
    }

    public function incomeActivity() {
        return $this->hasMany(IncomeActivity::class);
    }

    public function paymentCategory() {
        return $this->hasMany(PaymentCategory::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }

}
