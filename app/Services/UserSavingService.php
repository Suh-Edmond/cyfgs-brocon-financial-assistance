<?php

namespace App\Services;

use App\Constants\Constants;
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
        $saving = $this->findUserSaving($id, $user_id);
        if($saving->approve == PaymentStatus::PENDING){
            $saving->update([
                'amount_deposited'      => $request->amount_deposited,
                'comment'               => $request->comment,
            ]);
        }else {
            throw new BusinessValidationException("Saving cannot be updated after been ". $saving->approve, 403);
        }
    }

    public function getUserSavings($user_id, $request)
    {
        $user = User::findOrFail($user_id);
        $savings = $user->userSaving();
        if(isset($request->month)){
            $savings  = $savings->whereMonth('user_savings.created_at', $request->month);
        }
        if(isset($request->status) && $request->status != "ALL"){
            $savings = $savings->where('user_savings.approve', $request->status);
        }
        $savings = $savings->orderBy('created_at');
        $total = $this->calculateTotalSaving($savings->get());

        $paginated_savings = $savings->paginate($request->per_page);

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

        $savings = $this->findOrganisationUserSavings($request->organisation_id, $request->session_id, $request->month, $request->filter);

        $total_amount_deposited = $this->calculateOrganisationTotalSavings($savings->get());

        $paginated_savings = $savings->paginate($request->per_page);

        return new UserSavingCollection($savings->get(), $total_amount_deposited, $paginated_savings->total(), $paginated_savings->lastPage(),
            (int)$paginated_savings->perPage(), $paginated_savings->currentPage());
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

    public function getMemberSavingPerQuarter($code, $session_id, $start_quarter, $end_quarter)
    {
        $total = 0;
        $savings = UserSaving::where('approve', PaymentStatus::APPROVED)
                    ->where('session_id', $session_id)
                    ->whereBetween('created_at', [$start_quarter, $end_quarter])
                    ->selectRaw('SUM(amount_deposited) as amount')
                    ->get();
        if(isset($savings[0]->amount)){
            $total = $savings[0]->amount;
        }
        return new QuarterlyIncomeResource($code, Constants::MEMBERS_SAVINGS, [], $total);
    }

    public function getMemberSavingPerYear($year, $code)
    {
        $savings =  UserSaving::where('approve', PaymentStatus::APPROVED)
            ->where('session_id', $year)
            ->selectRaw('SUM(amount_deposited) as amount')
            ->get()[0]->amount;
        return new QuarterlyIncomeResource($code, Constants::MEMBERS_SAVINGS, [], $savings);
    }


    private function findUserSaving($id, $user_id)
    {
        return UserSaving::where('user_id', $user_id)->where('id', $id)->firstOrFail();
    }


    public function calculateTotalSaving($savings)
    {
        return collect($savings)->filter(function ($record) {
            return $record->approve !== PaymentStatus::DECLINED;
        })->map(function ($saving){
            return ($saving->amount_deposited - $saving->amount_used);
        })->sum();
    }


    public function findOrganisationUserSavings($organisation_id, $session_id, $month, $filter)
    {

        $savings = UserSaving::where('user_savings.session_id', $session_id);

        if(isset($month)){
            $savings = $savings->whereMonth('user_savings.created_at', $month);
        }
        if(isset($filter)){
            $savings = $savings->whereHas('user', function ($query) use ($filter){
                $query->where('name', 'LIKE', '%'.$filter.'%');
            });
        }
        return $savings->selectRaw('(SUM(user_savings.amount_deposited) - SUM(user_savings.amount_used)) as total_amount, user_id, session_id,
            user_savings.id, user_savings.amount_deposited, user_savings.comment, user_savings.approve, user_savings.amount_used, user_savings.created_at, user_savings.updated_at, user_savings.updated_by')
            ->groupBy('user_id')
            ->orderBy('user_savings.created_at');
    }


    public function getUserSavingsForDownload($request) {

        $savings = UserSaving::where('user_id', $request->user_id);

        if(isset($request->month)){
            $savings  = $savings->whereMonth('created_at', $request->month);
        }
        if(isset($request->status) && $request->status != "ALL"){
            $savings = $savings->where('approve', $request->status);
        }
        $savings = $savings->orderBy('created_at')->get();

        $total = $this->calculateTotalSaving($savings);

        return [$savings, $total];
    }

    public function getOrganisationSavingsForDownload($request)
    {
        return $this->findOrganisationUserSavings($request->organisation_id, $request->session_id, $request->month, $request->filter)->get();
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

        $data = UserSaving::where('user_savings.session_id', $request->session_id);

        if(!is_null($request->name)){
            $name = $request->name;
            $data = $data->whereHas('user', function ($query) use ($name){
                $query->where('name', 'LIKE', '%'.$name.'%');
            });
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
            $data = $data->whereMonth('user_savings.created_at', $request->month);
        }
        if(isset($request->filter)) {
            $filter = $request->filter;
            $data = $data->whereHas('user', function ($query) use ($filter){
                $query->where('name', 'LIKE', '%'.$filter.'%');
            });
        }

        $savings = $data->selectRaw('(SUM(user_savings.amount_deposited) - SUM(user_savings.amount_used)) as total_amount, user_id, session_id,
            user_savings.id, user_savings.amount_deposited, user_savings.comment, user_savings.approve, user_savings.amount_used, user_savings.created_at,
             user_savings.updated_at, user_savings.updated_by')
            ->groupBy('user_id')
            ->orderBy('user_savings.created_at', 'ASC');
        $total_amount_deposited = $this->calculateOrganisationTotalSavings($savings->get());
        $paginated_savings      = $savings->paginate($request->per_page);

        return new UserSavingCollection($savings->get(), $total_amount_deposited, $paginated_savings->total(), $paginated_savings->lastPage(),
            (int)$paginated_savings->perPage(), $paginated_savings->currentPage());
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
         $status_stat =  $this->getSavingsStatByStatus($request->session_id);

        return ['monthly_stat' => $monthly_stat, 'yearly_stat' => $yearly_stat, 'status_stat' => $status_stat];
    }

    public function getTotalYearlyMemberSavings($session_id, $user_id)
    {
        return UserSaving::where('user_id', $user_id)
                 ->where('approve', PaymentStatus::APPROVED)
                 ->where('session_id', $session_id)
                 ->selectRaw('SUM(amount_deposited) - SUM(amount_used) as balance_saving')->first();
    }

    private function getTotalMonthlySavings($request)
    {
        $total_monthly_savings = [];
        for ($counter = 1; $counter <= 12; $counter++){
            $savings = UserSaving::where('session_id', $request->session_id)
                                  ->where('approve', PaymentStatus::APPROVED)
                                  ->whereMonth('created_at', $counter)
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
            $savings = UserSaving::where('session_id', $session->id)
                                 ->where('approve', PaymentStatus::APPROVED)
                                 ->selectRaw('SUM(amount_deposited) - SUM(amount_used) as total_saving')->first();
            isset($savings->total_saving)? array_push($total_yearly_savings, ["amount" =>$savings->total_saving, "year" => $session->year]) : array_push($total_yearly_savings, 0);
        }
        return $total_yearly_savings;
    }

    private function getSavingsStatByStatus($current_session)
    {
        $savings_by_status = [];

        $savings = UserSaving::where('session_id', $current_session)->get();

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
