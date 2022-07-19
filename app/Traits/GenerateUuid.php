<?php
namespace App\Traits;

use Exception;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait GenerateUuid {

    protected static function boot () {
        parent::boot();

        static::creating(function ($model) {
            $user = Auth::user();
            try {
                $model->id =  Uuid::uuid4();

                if($user){
                    $model->created_by = Auth::user()->name;
                    $model->updated_by = Auth::user()->name;
                }
            }catch (Exception $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
