<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreYearlyBalance extends Model
{
    use HasFactory;

    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'session_id',
        'balance',
        'updated_by'
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
