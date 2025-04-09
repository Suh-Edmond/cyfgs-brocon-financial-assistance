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
}
