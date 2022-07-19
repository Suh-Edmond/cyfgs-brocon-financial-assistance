<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class IncomeActivity extends Model
{
    use GenerateUuid;
    protected $fillable = [
        'name',
        'description',
        'date',
        'amount',
        'venue',
        'organisation_id',
        'approve'
    ];

    public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'uuid';

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }
}
