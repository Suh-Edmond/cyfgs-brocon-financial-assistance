<?php
namespace App\Services;

use App\Interfaces\UserSavingInterface;
use App\Models\User;
use App\Models\UserSaving;
use Illuminate\Support\Facades\DB;

class UserServingService implements UserSavingInterface {

    public function createUserSaving($request)
    {
        $user = User::findOrFail($request->user_id);
        if(! $user){
            return response()->json(['message'=> 'User Saving not found', 'status' => '404'], 404);
        }
        UserSaving::create([
            'amount_deposited'      => $request->amount_deposited,
            'comment'               => $request->comment,
            'user_id'               => $user->id,
        ]);
    }

    public function updateUserSaving($request, $id, $user_id)
    {
        $user = $this->findUserSaving($id, $user_id);
        if(! $user){
            return response()->json(['message'=> 'User  Savingnot found', 'status' => '404'], 404);
        }
        $user->update([
            'amount_deposited'      => $request->amount_deposited,
            'comment'               => $request->comment,
        ]);
    }

    public function getUserSavings($user_id)
    {
        return UserSaving::where('user_id', $user_id)->toArray();
    }

    public function getUserSaving($id, $user_id)
    {
        $user_saving = $this->findUserSaving($id, $user_id);
        if(! $user_saving){
            return response()->json(['message'=> 'User Saving not found', 'status' => '404'], 404);
        }

        return $user_saving;
    }

    public function deleteUserSaving($id, $user_id)
    {
        $user_saving = $this->findUserSaving($id, $user_id);
        if(! $user_saving){
            return response()->json(['message'=> 'User Saving not found', 'status' => '404'], 404);
        }

        $user_saving->delete();
    }

    public function approveUserSaving($id)
    {
        $user_saving = UserSaving::findOrFail($id);
        if(! $user_saving){
            return response()->json(['message'=> 'User Saving not found', 'status' => '404'], 404);
        }

        $user_saving->update(['approve' => true]);
    }

    public function calculateUserSaving($id, $expenditure_item_id)
    {

    }

    private function findUserSaving($id, $user_id)
    {
        $saving = UserSaving::select('user_savings.*')
                    ->join('users', ['users.id' => 'user_savings.user_id'])
                    ->where('users.id', $user_id)
                    ->where('user_savings.id', $id)
                    ->first();
        return $saving;
    }


}
