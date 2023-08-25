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
            'session_id'            => $current_session->id,
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
            throw new BusinessValidationException("Saving cannot be updated after been approved or declined");
        }
    }

    public function getUserSavings($user_id, $request)
    {

        $savings = DB::table('user_savings')
                    ->join('users', 'users.id', '=', 'user_savings.user_id')
                    ->where('user_id', $user_id);
        if(isset($request->month)){
            $savings  = $savings->whereMonth('user_savings.created_at', $request->month);
        }
        if(isset($request->status) && $request->status != "ALL"){
            $savings = $savings->where('user_savings.approve', $request->status);
        }
        $savings = $savings->select('user_savings.*', 'users.email', 'users.name', 'users.telephone');
        $total = $this->calculateTotalSaving($savings->get());
        $paginated_savings = $savings->orderBy('user_savings.created_at')->paginate($request->per_page);

        return new UserSavingCollection($paginated_savings, $total, $paginated_savings->total(), $paginated_savings->lastPage(),
            $paginated_savings->perPage(), $paginated_savings->currentPage());
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


    public function getAllUserSavingsByOrganisation($request)
    {
        $savings = $this->findOrganisationUserSavings($request->organisation_id, $request->session_id, $request->month);
        $total_amount_deposited = $this->calculateOrganisationTotalSavings($savings->get());
        $paginated_savings = $savings->paginate($request->per_page);


        return new UserSavingCollection($savings->get(), $total_amount_deposited, $paginated_savings->total(), $paginated_savings->lastPage(),
            $paginated_savings->perPage(), $paginated_savings->currentPage());
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
        return collect($savings)->map(function ($saving){
            return ($saving->amount_deposited - $saving->amount_used);
        })->sum();
    }


    public function findOrganisationUserSavings($organisation_id, $session_id, $month)
    {
        $savings = DB::table('user_savings')
            ->join('sessions', 'sessions.id', '=', 'user_savings.session_id')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('organisations', 'users.organisation_id', '=', 'organisations.id')
            ->where('organisations.id', $organisation_id);
        if(isset($session_id)){
            $savings = $savings->where('user_savings.session_id', $session_id);
        }
        if(isset($month)){
            $savings = $savings->whereMonth('user_savings.created_at', $month);
        }
        $savings = $savings->selectRaw('(SUM(user_savings.amount_deposited) - SUM(user_savings.amount_used)) as total_amount, users.id as user_id,
            users.name as name, users.email as email, users.telephone as telephone, sessions.id as session_id, sessions.year as session_year, sessions.status as session_status,
            user_savings.id, user_savings.amount_deposited, user_savings.comment, user_savings.approve, user_savings.amount_used, user_savings.created_at, user_savings.updated_at, user_savings.updated_by')
        ->groupBy('user_id')
        ->orderBy('users.name', 'ASC');

        return $savings;
    }


    public function getUserSavingsForDownload($request) {

        $savings = DB::table('user_savings')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->where('user_id', $request->user_id);
        if(isset($request->month)){
            $savings  = $savings->whereMonth('user_savings.created_at', $request->month);
        }
        if(isset($request->status) && $request->status != "ALL"){
            $savings = $savings->where('user_savings.approve', $request->status);
        }
        $savings = $savings->select('user_savings.*', 'users.email', 'users.name', 'users.telephone')->orderBy('user_savings.created_at')->get();
        $total = $this->calculateTotalSaving($savings);

        return [$savings, $total];
    }

    public function getOrganisationSavingsForDownload($request)
    {
        return $this->findOrganisationUserSavings($request->organisation_id, $request->session_id, $request->month)->get();
    }

    public function  getMembersSavingsByOrganisation($request)
    {
        return  DB::table('user_savings')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('organisations', 'users.organisation_id', '=', 'organisations.id')
            ->join('sessions', 'sessions.id', '=', 'user_savings.session_id')
            ->where('organisations.id', $request->organisation_id);
    }

    public function filterSavings($request)
    {

        $data = $this->getMembersSavingsByOrganisation($request);
        $data = $data->where('user_savings.session_id', $request->session_id);

        if (!is_null($request->user_id)) {
            $data = $data->where('user_savings.user_id', $request->user_id);
        }
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

        $data = $data->select('user_savings.*', 'users.id as user_id',
            'users.name as name', 'users.email as email', 'users.telephone as telephone', 'sessions.id as session_id', 'sessions.year as session_year', 'sessions.status as session_status')
            ->orderBy('user_savings.created_at', 'ASC')
            ->get();
        return $data;
    }

    public function deductSavingAfterContribution($user_id, $amount)
    {
        $saving = UserSaving::where('user_id', $user_id)->where('amount_deposited', '>=', $amount)->first();
        $saving->update([
            'amount_used' => $amount + $saving->amount_used
        ]);
    }

    public function getSavingsStatistics($request)
    {
        $monthly_stat = $this->getTotalMonthlySavings($request);
        $yearly_stat = $this->getTotalYearlySavings();
        $status_stat =  $this->getSavingsStatByStatus();

        return ['monthly_stat' => $monthly_stat, 'yearly_stat' => $yearly_stat, 'status_stat' => $status_stat];
    }

    private function getTotalMonthlySavings($request)
    {
        $total_monthly_savings = [];
        for ($counter = 1; $counter <= 12; $counter++){
            $savings = DB::table('user_savings')
                ->join('sessions', 'sessions.id', '=', 'user_savings.session_id')
                ->where('sessions.id', $request->session_id)
                ->where('user_savings.approve', PaymentStatus::APPROVED)
                ->whereMonth('user_savings.created_at', $counter)
                ->selectRaw('SUM(amount_deposited) - SUM(amount_used) as total_saving')->first();
            isset($savings->total_saving)? array_push($total_monthly_savings, $savings->total_saving) : array_push($total_monthly_savings, 0);
        }
        return $total_monthly_savings;
    }

    private function getTotalYearlySavings()
    {
        $total_yearly_savings = [];
        $sessions = $this->sessionService->getAllSessions();
        foreach ($sessions as $session){
            $savings = DB::table('user_savings')
                ->join('sessions', 'sessions.id', '=', 'user_savings.session_id')
                ->where('sessions.id', $session->id)
                ->where('user_savings.approve', PaymentStatus::APPROVED)
                ->selectRaw('SUM(amount_deposited) - SUM(amount_used) as total_saving')->first();
            isset($savings->total_saving)? array_push($total_yearly_savings, ["amount" =>$savings->total_saving, "year" => $session->year]) : array_push($total_yearly_savings, 0);
        }
        return $total_yearly_savings;
    }

    private function getSavingsStatByStatus()
    {
        $savings_by_status = [];
        $current_session = $this->sessionService->getCurrentSession();
        $savings = DB::table('user_savings')
            ->join('sessions', 'sessions.id', '=', 'user_savings.session_id')
            ->where('sessions.id', $current_session->id)
            ->select('user_savings.*')
            ->get();
        $saving_collect = collect($savings)->groupBy('approve')->toArray();
        if($saving_collect < 0){
            return [0, 0, 0];
        }else {
            isset($saving_collect['PENDING']) ? array_push($savings_by_status, count($saving_collect['PENDING'])): array_push($savings_by_status, 0);
            isset($saving_collect['APPROVED']) ? array_push($savings_by_status, count($saving_collect['APPROVED'])): array_push($savings_by_status,  0);
            isset($saving_collect['DECLINED']) ? array_push($savings_by_status, count($saving_collect['DECLINED'])): array_push($savings_by_status, 0);
        }

        return $savings_by_status;
    }
}
