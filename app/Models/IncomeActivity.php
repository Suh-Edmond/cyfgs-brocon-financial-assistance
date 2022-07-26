<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class IncomeActivity extends Model
{

    protected $fillable = [
        'name',
        'description',
        'date',
        'amount',
        'venue',
        'organisation_id',
        'approve'
    ];



    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }
}
