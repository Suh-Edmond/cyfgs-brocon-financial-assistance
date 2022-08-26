<?php

namespace App\Services;

use App\Http\Resources\UserSavingCollection;
use App\Interfaces\UserSavingInterface;
use App\Models\User;
use App\Models\UserSaving;

class UsersavingService implements UserSavingInterface
{

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

        $savings = UserSaving::where('user_id', $user_id)->get();

        $total = $this->calculateTotalSaving($savings);

        return new UserSavingCollection($savings, $total);
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

    public function getAllUserSavingsByOrganisation($id)
    {
        $savings = $this->findOrganisationUserSavings($id);

        $total = $this->calculateTotalSaving($savings);

        return new UserSavingCollection($savings, $total);
    }

    public function findUserSavingByStatus($status, $id)
    {
        $savings = UserSaving::select('user_savings.*')
            ->join('users', ['users.id' => 'user_savings.user_id'])
            ->join('organisations', ['users.organisation_id' => 'organisations.id'])
            ->where('organisations.id', $id)
            ->where('user_savings.approve', $status)
            ->orderBy('users.name', 'ASC')
            ->get();

        $total = $this->calculateTotalSaving($savings);

        return new UserSavingCollection($savings, $total);
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


    public function calculateTotalSaving($savings)
    {
        $total = 0;
        foreach ($savings as $saving) {
            $total += $saving->amount_deposited;
        }

        return $total;
    }

    public function findOrganisationUserSavings($organisation_id)
    {
        $savings = UserSaving::select('user_savings.*')
            ->join('users', ['users.id' => 'user_savings.user_id'])
            ->join('organisations', ['users.organisation_id' => 'organisations.id'])
            ->where('organisations.id', $organisation_id)
            ->orderBy('users.name', 'ASC')
            ->get();

        return $savings;
    }
}
