<?php
namespace App\Services;

use App\Interfaces\UserSavingInterface;
use App\Models\User;
use App\Models\UserSaving;

class UserServingService implements UserSavingInterface {

    public function createUserSaving($request)
    {
        $user = User::findOrFail($request->user_id);
        UserSaving::create([
            'amount_deposited'      => $request->amount_deposited,
            'comment'               => $request->comment,
            'user_id'               => $user->id,
        ]);
    }

    public function updateUserSaving($request, $id, $user_id)
    {
        $user = $this->findUserSaving($id, $user_id);
        $user->update([
            'amount_deposited'      => $request->amount_deposited,
            'comment'               => $request->comment,
        ]);
    }

    public function getUserSavings($user_id)
    {
        return UserSaving::where('user_id', $user_id);
    }

    public function getUserSaving($id, $user_id)
    {
        $user_saving = $this->findUserSaving($id, $user_id);

        return $user_saving;
    }

    public function deleteUserSaving($id, $user_id)
    {
        $user_saving = $this->findUserSaving($id, $user_id);

        $user_saving->delete();
    }

    public function approveUserSaving($id)
    {
        $user_saving = UserSaving::findOrFail($id);
        $user_saving->approve = 1;
        $user_saving->save();
    }

    public function calculateUserSaving($id, $expenditure_item_id)
    {

    }

    public function getAllUserSavingsByOrganisation($id)
    {
        $savings = UserSaving::select('user_savings.*, SUM(user_savings.amount_deposited) as total_amount')
                                ->join('users', ['users.id' => 'user_savings.user_id'])
                                ->join('organisations', ['users.organisation_id' => 'organisations.id'])
                                ->orderBy('name', 'ASC')
                                ->get();
        return $savings;
    }

    private function findUserSaving($id, $user_id)
    {
        $saving = UserSaving::select('user_savings.*')
                    ->join('users', ['users.id' => 'user_savings.user_id'])
                    ->where('users.id', $user_id)
                    ->where('user_savings.id', $id)
                    ->firstOrFail();
        return $saving;
    }


}
