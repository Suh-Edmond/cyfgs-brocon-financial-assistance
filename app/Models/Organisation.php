<?php

namespace App\Models;
use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{

    protected $fillable = [
        'name',
        'description',
        'telephone',
        'email',
        'address',
        'logo',
        'salutation',
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

    public function user() {
        return $this->hasMany(User::class);
    }
}
