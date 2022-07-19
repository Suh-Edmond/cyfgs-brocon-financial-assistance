<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureItem extends Model
{
    use GenerateUuid;
    protected $fillable = [
        'name',
        'amount',
        'scan_picture',
        'comment',
        'approve',
        'venue',
        'expenditure_category_id'
    ];

    public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'uuid';

    public function expenditureDetail() {
        return $this->hasMany(ExpenditureDetail::class);
    }
}
