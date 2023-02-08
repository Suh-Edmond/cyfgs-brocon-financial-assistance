<?php

namespace App\Services;

use App\Http\Resources\UserSavingCollection;
use App\Interfaces\UserSavingInterface;
use App\Models\User;
use App\Models\UserSaving;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\DB;

class UsersavingService implements UserSavingInterface
{
    use HelpTrait;

    public function createUserSaving($request)
    {
        $user = User::findOrFail($request->user_id);
        UserSaving::create([
            'amount_deposited'      => $request->amount_deposited,
            'comment'               => $request->comment,
            'user_id'               => $user->id,
            'updated_by'            => $request->user()->name
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
        return $this->findUserSaving($id, $user_id);
    }


    public function deleteUserSaving($id, $user_id)
    {
        $user_saving = $this->findUserSaving($id, $user_id);

        $user_saving->delete();
    }


    public function approveUserSaving($id, $type)
    {
        $user_saving = UserSaving::findOrFail($id);
        $user_saving->approve = $type;
        $user_saving->save();
    }


    public function getAllUserSavingsByOrganisation($id)
    {
        $savings = $this->findOrganisationUserSavings($id);

        $total_amount_deposited = $this->calculateOrganisationTotalSavings($savings);

        return new UserSavingCollection($savings, $total_amount_deposited);
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
        return UserSaving::select('user_savings.*')
            ->join('users', ['users.id' => 'user_savings.user_id'])
            ->where('users.id', $user_id)
            ->where('user_savings.id', $id)
            ->firstOrFail();
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
        return DB::table('user_savings')
            ->leftJoin('users', 'users.id', '=', 'user_savings.user_id')
            ->leftJoin('organisations', 'users.organisation_id', '=', 'organisations.id')
            ->where('organisations.id', $organisation_id)
            ->selectRaw('SUM(user_savings.amount_deposited) as total_amount_deposited, user_savings.*')
            ->groupBy('user_savings.user_id')
            ->orderBy('users.created_at', 'DESC')
            ->get();
    }


    public function getUserSavingsForDownload($request) {
        $savings = UserSaving::where('user_id', $request->user_id);
        if($request->status != "null"){
            $savings = $savings->where('approve', $this->convertStatusToNumber($request->status));
        }
        return $savings->get();
    }

    public function getOrganisationSavingsForDownload($id)
    {
        return $this->findOrganisationUserSavings($id);
    }

    public function  getMembersSavingsByName($request)
    {
        return  DB::table('user_savings')
                ->leftJoin('users', 'users.id', '=', 'user_savings.user_id')
                ->leftJoin('organisations', 'users.organisation_id', '=', 'organisations.id')
                ->where('organisations.id', $request->organisation_id);
        //add filter for the current year
    }

    public function filterSavings($request)
    {

        $data = $this->getMembersSavingsByName($request);
        if(!is_null($request->name)){
            $data = $data->where('users.name', 'LIKE', '%'.$request->name.'%');
        }
        if(!is_null($request->date)){
            $data = $data->whereDate('user_savings.created_at', $request->date);
        }
        if($request->amount_deposited > 0) {
            $data = $data->where('user_savings.amount_deposited', $request->amount_deposited);
        }
        if(!is_null($request->status) && $request->status != "ALL") {
            $data = $data->where('user_savings.approve', $request->status);
        }
        if(!is_null($request->year)) {
            $data = $data->whereYear('user_savings.created_at', $request->year);
        }
        if (!is_null($request->month)) {
            $data = $data->whereMonth('user_savings.created_at', $this->convertMonthNameToNumber($request->month));
        }

        $data = $data->select('user_savings.*')
                ->orderBy('user_savings.created_at', 'ASC')
                ->get();

        return $data;
    }

}
