<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'amount',
        'status',
        'updated_by',
        'is_compulsory',
        'frequency'
    ];

    public function memberRegistrations()
    {
        return $this->hasMany(MemberRegistration::class);
    }
}
