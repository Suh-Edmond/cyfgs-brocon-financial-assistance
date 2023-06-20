<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\QuarterlyIncomeResource;
use App\Http\Resources\UserSavingCollection;
use App\Interfaces\UserSavingInterface;
use App\Models\User;
use App\Models\UserSaving;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\DB;

class UserSavingService implements UserSavingInterface
{
    use HelpTrait;
    private SessionService  $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function createUserSaving($request)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $user = User::findOrFail($request->user_id);
        UserSaving::create([
            'amount_deposited'      => $request->amount_deposited,
            'comment'               => $request->comment,
            'user_id'               => $user->id,
            'updated_by'            => $request->user()->name,
            'session_id'            => $current_session->id
        ]);
    }

    public function updateUserSaving($request, $id, $user_id)
    {
        $user = $this->findUserSaving($id, $user_id);
        if($user->approve == PaymentStatus::PENDING){
            $user->update([
                'amount_deposited'      => $request->amount_deposited,
                'comment'               => $request->comment,
            ]);
        }else {
            throw new BusinessValidationException("Income activity cannot be updated after been approved or declined");
        }
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


    public function getAllUserSavingsByOrganisation($id, $session_id)
    {
        $savings = $this->findOrganisationUserSavings($id, $session_id);

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

    public function getMemberSavingPerQuarter($quarter_num, $current_year, $code)
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];

        $savings =  DB::table('user_savings')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('sessions', 'sessions.id' , '=', 'user_savings.session_id')
            ->where('user_savings.approve', PaymentStatus::APPROVED)
            ->whereBetween('user_savings.created_at', [$start_quarter, $end_quarter])
            ->selectRaw('SUM(user_savings.amount_deposited) as amount')
            ->get()[0]->amount;
        return new QuarterlyIncomeResource($code, "Member's Savings", [], $savings);
    }

    public function getMemberSavingPerYear($year, $code)
    {
        $savings =  DB::table('user_savings')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('sessions', 'sessions.id' , '=', 'user_savings.session_id')
            ->where('user_savings.approve', PaymentStatus::APPROVED)
            ->where('user_savings.session_id', $year)
            ->selectRaw('SUM(user_savings.amount_deposited) as amount')
            ->get()[0]->amount;
        return new QuarterlyIncomeResource($code, "Member's Savings", [], $savings);
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


    public function findOrganisationUserSavings($organisation_id, $session_id)
    {
        return DB::table('user_savings')
            ->join('sessions', 'sessions.id', '=', 'user_savings.session_id')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('organisations', 'users.organisation_id', '=', 'organisations.id')
            ->where('organisations.id', $organisation_id)
            ->where('sessions.id', $session_id)
            ->selectRaw('SUM(user_savings.amount_deposited) as total_amount_deposited, user_savings.*, users.id as user_id,
            users.name as name, users.email as email, users.telephone as telephone, sessions.id as session_id, sessions.year as session_year, sessions.status as session_status')
            ->groupBy('user_savings.user_id')
            ->orderBy('users.name', 'ASC')
            ->get();
    }


    public function getUserSavingsForDownload($request) {
        $savings = DB::table('user_savings')->join('users', 'users.id', '=', 'user_savings.user_id')
                    ->where('user_savings.user_id', $request->user_id);
        if(!is_null($request->status)){
            $savings = $savings->where('approve', $this->convertStatusToNumber($request->status));
        }
        $savings = $savings->selectRaw('SUM(user_savings.amount_deposited) as total_amount_deposited, user_savings.*, users.*');

        $savings = $savings->orderBy('user_savings.created_at', 'DESC')->get();

        return $savings;
    }

    public function getOrganisationSavingsForDownload($id, $session_id)
    {
        return $this->findOrganisationUserSavings($id, $session_id);
    }

    public function  getMembersSavingsByName($request)
    {
        return  DB::table('user_savings')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('organisations', 'users.organisation_id', '=', 'organisations.id')
            ->join('sessions', 'sessions.id', '=', 'user_savings.session_id')
            ->where('organisations.id', $request->organisation_id);
    }

    public function filterSavings($request)
    {

        $data = $this->getMembersSavingsByName($request);
        $data = $data->where('user_savings.session_id', $request->session_id);

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
        if (!is_null($request->month)) {
            $data = $data->whereMonth('user_savings.created_at', $this->convertMonthNameToNumber($request->month));
        }
        if (!is_null($request->user_id)) {
            $data = $data->where('user_savings.user_id', $request->user_id);
        }

        $data = $data->select('user_savings.*', 'users.id as user_id',
            'users.name as name', 'users.email as email', 'users.telephone as telephone', 'sessions.id as session_id', 'sessions.year as session_year', 'sessions.status as session_status')
            ->orderBy('user_savings.created_at', 'ASC')
            ->get();
        return $data;
    }

}
