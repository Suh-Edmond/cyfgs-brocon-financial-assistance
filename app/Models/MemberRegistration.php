<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRegistration extends Model
{
    use HasFactory;
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'amount',
        'year',
        'user_id',
        'updated_by',
        'session_id',
        'month_name',
        'registration_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session() {
        return $this->belongsTo(Session::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function transactionHistory()
    {
        return $this->hasOne(TransactionHistory::class, "reference_data");
    }
}
