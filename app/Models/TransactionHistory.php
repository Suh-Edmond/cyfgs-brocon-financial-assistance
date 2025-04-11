<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory;
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'old_amount_deposited',
        'new_amount_deposited',
        'reason',
        'reference_data',
        'updated_by',
        'approve',
        'code',
    ];

    public function activitySupport()
    {
        return $this->belongsTo(ActivitySupport::class);
    }

    public function expenditureDetail()
    {
        return $this->belongsTo(ExpenditureDetail::class);
    }

    public function expenditureItem()
    {
        return $this->belongsTo(ExpenditureItem::class);
    }

    public function incomeActivity()
    {
        return $this->belongsTo(IncomeActivity::class);
    }

    public function memberRegistration()
    {
        return $this->belongsTo(MemberRegistration::class);
    }

    public function userContribution()
    {
        return $this->belongsTo(UserContribution::class);
    }

    public function userSaving()
    {
        return $this->belongsTo(UserSaving::class);
    }
}
