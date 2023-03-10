<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static findOrFail($id)
 * @method static select(string $string)
 */
class ExpenditureItem extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'name',
        'amount',
        'comment',
        'venue',
        'date',
        'expenditure_category_id',
        'scan_picture',
        'updated_by',
        'payment_item_id'
    ];

    public function expenditureDetails() {
        return $this->hasMany(ExpenditureDetail::class);
    }

    public function expenditureCategory()
    {
        return $this->belongsTo(ExpenditureCategory::class);
    }

    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }
}
