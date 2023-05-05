<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

/**
 * @method static where(string $string, $user_id)
 * @method static select(string $string)
 */
class UserSaving extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'amount_deposited',
        'comment',
        'user_id',
        'updated_by',
        'session_id'
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function session() {
        return $this->hasMany(Session::class);
    }

}
