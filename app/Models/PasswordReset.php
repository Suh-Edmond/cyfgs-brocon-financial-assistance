<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'token',
        'user_id',
        'created_at',
        'expire_at',
        'updated_at'
    ];
}
