<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $ACTIVE)
 */
class Session extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'year',
        'updated_by'
    ];

    public function activitySupports()
    {
        return $this->hasMany(ActivitySupport::class);
    }

    public function expenditureItems()
    {
        return $this->hasMany(ExpenditureItem::class);
    }

    public function incomeActivities()
    {
        return $this->hasMany(IncomeActivity::class);
    }

    public function memberRegistrations()
    {
        return $this->hasMany(MemberRegistration::class);
    }

    public function paymentItems()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function userContributions()
    {
        return $this->hasMany(UserContribution::class);
    }

    public function userSavings()
    {
        return $this->hasMany(UserSaving::class);
    }
}
