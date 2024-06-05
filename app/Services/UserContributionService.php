<?php

namespace App\Services;

use App\Constants\PaymentItemFrequency;
use App\Constants\PaymentItemType;
use App\Constants\RegistrationFrequency;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\MemberContributedItemResource;
use App\Http\Resources\MemberPaymentItemResource;
use App\Http\Resources\SessionResource;
use App\Interfaces\UserContributionInterface;
use App\Models\MemberRegistration;
use App\Models\PaymentItem;
use App\Models\Registration;
use App\Models\User;
use App\Models\UserContribution;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\DB;
use App\Constants\PaymentStatus;
use App\Http\Resources\UserContributionCollection;


class UserContributionService implements UserContributionInterface {

    use HelpTrait;
    private SessionService $sessionService;
    private UserSavingService $userSavingService;
    private PaymentItemService $paymentItemService;

    public function __construct(SessionService $sessionService, UserSavingService $userSavingService, PaymentItemService $paymentItemService)
    {
        $this->sessionService = $sessionService;
        $this->userSavingService = $userSavingService;
        $this->paymentItemService = $paymentItemService;
    }

    public function createUserContribution($request)
    {
        $this->saveContribution($request, $request->user_id, $request->user()->name, $this->sessionService->getCurrentSession()->id);
    }

    public function updateUserContribution($request, $id)
    {
//        $user_contribution = $this->findUserContributionById($id);
//
////        $amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_contribution->user_id, $user_contribution->payment_item_id) - $user_contribution->amount_deposited;
//
//        $total_amount_contributed = $amount_contributed + $request->amount_deposited;
//
//        $balance = $user_contribution->paymentItem->amount - $total_amount_contributed;
//
//        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $user_contribution->paymentItem->amount);
//
//        $status = $this->getUserContributionStatus($user_contribution->paymentItem->amount, $total_amount_contributed);
//
//        if(!$hasCompleted){
//            if($user_contribution->approve == PaymentStatus::PENDING){
//                $user_contribution->update([
//                    'amount_deposited' => $request->amount_deposited,
//                    'comment'          => $request->comment,
//                    'scan_picture'     => $request->scan_picture,
//                    'balance'          => $balance,
//                    'status'           => $status
//                ]);
//            }else {
//                throw new BusinessValidationException("Income activity cannot be updated after been approved or declined");
//            }
//        }
    }

    public function getContributionsByItem($payment_item_id)
    {
        $user_contributions = UserContribution::select('user_contributions.*')
                                                ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                                                ->where('user_contributions.payment_item_id', $payment_item_id)
                                                ->orderBy('user_contributions.created_at', 'DESC')
                                                ->get();

        return new UserContributionCollection($user_contributions, 0);
    }

    public function getUserContributionsByUser($user_id)
    {
        return UserContribution::select('user_contributions.*')
                                                ->join('users', ['users.id' => 'user_contributions.user_id'])
                                                ->where('user_contributions.user_id', $user_id)
                                                ->get()
                                                ->groupBy('user_contributions.payment_item_id');

    }

    public function getContributionByUserAndItem($payment_item_id, $user_id, $request)
    {
        $payment_item_durations = array();
        $unpaid_durations = array();
        $payment_item = PaymentItem::find($payment_item_id);
        $contributions =  $this->getContributionByUserAndPaymentItem($payment_item_id, $user_id)
                                    ->select(  'user_contributions.*')
                                    ->orderBy('user_contributions.created_at', 'DESC');
        if(!is_null($request->transaction_status) && $request->transaction_status !== "ALL"){
            $contributions = $contributions->where('user_contributions.status', $request->transaction_status);
        }
        if(!is_null($request->payment_status) && $request->payment_status !== "ALL"){
            $contributions = $contributions->where('user_contributions.approve', $request->payment_status);
        }
        $total_contribution = collect($contributions->get())->sum('amount_deposited');
        $total_amount_payable = $this->getTotalPaymentItemAmountByQuarters($payment_item);
        $total_balance = ($total_amount_payable - $total_contribution);
        $percentage = $this->computePercentageContributed($total_contribution, $total_amount_payable);

        if($payment_item->frequency == PaymentItemFrequency::MONTHLY){
            $unpaid_durations = $this->getMemberUnPayMonths($payment_item->frequency, $payment_item->created_at, $contributions->get());
        }elseif ($payment_item->frequency == PaymentItemFrequency::QUARTERLY){
            $unpaid_durations = $this->getMemberUnPayQuarters($payment_item->frequency, $payment_item->created_at, $contributions->get());
        }
        if ($payment_item->frequency == PaymentItemFrequency::QUARTERLY){
            $payment_item_durations = $this->getPaymentItemQuartersBySession($payment_item->frequency, $payment_item->created_at);
        }
        if ($payment_item->frequency == PaymentItemFrequency::MONTHLY){
            $payment_item_durations = $this->getPaymentItemMonthsBySession($payment_item->frequency, $payment_item->created_at);
        }

        $paginated_contribution = isset($request->per_page) ? $contributions->paginate($request->per_page): $contributions->get();

        $total = isset($request->per_page) ? $paginated_contribution->total() : count($paginated_contribution);
        $last_page = isset($request->per_page) ? $paginated_contribution->lastPage(): 0;
        $per_page = isset($request->per_page) ? (int)$paginated_contribution->perPage() : 0;
        $current_page = isset($request->per_page) ? $paginated_contribution->currentPage() : 0;


        return new UserContributionCollection($paginated_contribution, $total_contribution, $total_balance, $unpaid_durations, $total_amount_payable,  $total, $last_page,
            $per_page, $current_page, $percentage, $payment_item_durations, 1, $payment_item);
    }

    public function deleteUserContribution($id)
    {
        $user_contribution =  $this->findUserContributionById($id);

        $user_contribution->delete();
    }

    public function approveUserContribution($request)
    {
          $user_contributions = UserContribution::
                                 join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
                                ->join('users', 'users.id', '=', 'user_contributions.user_id')
                                ->join('sessions', 'sessions.id', '=', 'user_contributions.session_id')
                                ->where('payment_items.id', $request->payment_item_id)
                                ->where('users.id', $request->user_id)
                                ->where('sessions.id', $request->session_id)->select('user_contributions.*')->get();
          for ($counter = 0; $counter < count($user_contributions); $counter++){
              $user_contributions[$counter]->approve = $request->type;
              $user_contributions[$counter]->save();
          }
    }

    public function filterContributions($request)
    {
        $payment_item_durations = array();
        $payment_item = PaymentItem::find($request->payment_item_id);
        $contributions = $this->getUserContributions($request);
        $contributions = $contributions->selectRaw('SUM(user_contributions.amount_deposited) as total_amount_deposited, user_contributions.*')
            ->groupBy('user_id')->orderBy('user_contributions.created_at', 'DESC');
        $total_contribution = $this->computeTotalOrganisationContribution($contributions);
        $expectedData = $this->computeTotalExpectedPaymentItemAmount($payment_item);
        $total_amount_payable = $expectedData[0];
        $member_size = $expectedData[1];
        $total_balance = $this->computeTotalBalanceByPaymentItem($payment_item, $total_contribution);
        $percentage = $this->computePercentageContributed($total_contribution, $total_amount_payable);

        if ($payment_item->frequency == PaymentItemFrequency::QUARTERLY){
            $payment_item_durations = $this->getPaymentItemQuartersBySession($payment_item->frequency, $payment_item->created_at);
        }
        if ($payment_item->frequency == PaymentItemFrequency::MONTHLY){
            $payment_item_durations = $this->getPaymentItemMonthsBySession($payment_item->frequency, $payment_item->created_at);
        }
        if(isset($request->filter) && count($request->filter) > 5){
            $contributions = $contributions->where('users.name', 'LIKE', '%'.$request->filter.'%');
        }
        $contributions = !is_null($request->per_page) ? $contributions->paginate($request->per_page): $contributions->get();

        $total = !is_null($request->per_page) ? $contributions->total() : count($contributions);
        $last_page = !is_null($request->per_page) ? $contributions->lastPage(): 0;
        $per_page = !is_null($request->per_page) ? (int)$contributions->perPage() : 0;
        $current_page = !is_null($request->per_page) ? $contributions->currentPage() : 0;


        return new UserContributionCollection($contributions, $total_contribution, $total_balance, array(), $total_amount_payable, $total, $last_page,
            $per_page, $current_page, $percentage, $payment_item_durations, $member_size, $payment_item);
    }

    public function getContribution($id)
    {
        return $this->findUserContributionById($id);
    }


    public function getTotalBalanceByUserAndItem($payment_item_amount, $total)
    {
        return ($payment_item_amount - $total);
    }


    public function getTotalAmountPaidByUserForTheItem($user_id, $payment_item_id, $month, $quarter, $frequency)
    {
        $contributions =  $this->getContributionByUserAndPaymentItem($payment_item_id, $user_id);
        if($frequency == PaymentItemFrequency::QUARTERLY){
            $contributions = $contributions->where('quarterly_name', $quarter);
        }
        if($frequency == PaymentItemFrequency::MONTHLY){
            $contributions = $contributions->where('month_name', $month);
        }
        $contributions = $contributions->sum('user_contributions.amount_deposited');
        return $contributions;
    }


    public function calculateTotalContributions($user_contributions)
    {
        $total = 0;
        if(isset($user_contributions)){
            foreach($user_contributions as $user_contribution){
                $total += $user_contribution->amount_deposited;
            }
        }

        return $total;
    }


    public function filterContributionByYear($payment_item_id, $year)
    {
        $unpaid_durations = array();
        $payment_item_durations = array();
        $payment_item = PaymentItem::find($payment_item_id);
        $user_contributions = UserContribution::join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                              ->where('payment_items.id', $payment_item_id)
                              ->where('user_contributions.session_id', $year)
                              ->orderBy('payment_items.name', 'ASC')
                              ->select('user_contributions.*', 'payment_items.amount');
        $total = isset($user_contributions) ? collect($user_contributions->get())->sum('amount_deposited'):0;
        $paginated_data = $user_contributions->paginate(7);
        $total_amount_payable = $this->getTotalPaymentItemAmountByQuarters($payment_item);
        $balance = $total != 0 ? $total_amount_payable - $total :0;
        $percentage = $this->computePercentageContributed($total, $total_amount_payable);
        $member_size = User::all()->count('id');

        if($payment_item->frequency == PaymentItemFrequency::QUARTERLY){
            $unpaid_durations = $this->getMemberUnPayQuarters($payment_item->frequency, $payment_item->created_at, $user_contributions->get());
        }
        if ($payment_item->frequency == PaymentItemFrequency::MONTHLY){
            $unpaid_durations =  $this->getMemberUnPayMonths($payment_item->frequency, $payment_item->created_at, $user_contributions->get());
        }
        if ($payment_item->frequency == PaymentItemFrequency::QUARTERLY){
            $payment_item_durations = $this->getPaymentItemQuartersBySession($payment_item->frequency, $payment_item->created_at);
        }
        if ($payment_item->frequency == PaymentItemFrequency::MONTHLY){
            $payment_item_durations = $this->getPaymentItemMonthsBySession($payment_item->frequency, $payment_item->created_at);
        }

        return new UserContributionCollection($user_contributions, $total, $balance, $unpaid_durations, $total_amount_payable, $paginated_data->total(), $paginated_data->lastPage()
            , (int)$paginated_data->perPage(), $paginated_data->currentPage(), $percentage, $payment_item_durations, $member_size, $payment_item);
    }


    public function bulkPayment($request, $auth_user)
    {
        $user = User::findOrFail($request->user_id);
        $payload = collect($request);
        for ($counter = 0; $counter < $payload->count() - 2; $counter++){
           $json_data = $payload[$counter];
            if($json_data->code == 'REGISTRATION'){
                $this->saveRegistration($json_data, $user, $auth_user);
            } elseif ($json_data->code == 'CONTRIBUTION'){
                $this->saveContribution($json_data, $user->id, $auth_user, $request->current_session_id);
            }
        }
    }


    public function getMemberDebt($request)
    {
        $reg_debts = $this->getMemberRegistration($request->user_id);
        $debts = $this->getMemberOwingItems($request->user_id, $request->session_id);
        return array_merge($reg_debts, $debts);
    }


    public function getMemberContributedItems($user_id, $year)
    {
         $paid_debts = [];
         $current_session = $this->sessionService->getCurrentSession();
         $reg = $this->getRegistration($user_id);
         $contributions = $this->getAllMemberContribution($user_id, $current_session->id);
         foreach ($contributions as $contribution){
             $contributedItem = new MemberContributedItemResource($contribution->id, $contribution->payment_item_id,$contribution->payment_item_amount, $contribution->name,
                 $contribution->amount_deposited, $contribution->balance, $contribution->status, $contribution->approve, $contribution->created_at, $contribution->year, $contribution->frequency,
             $contribution->month_name, $contribution->quarterly_name, $contribution->updated_by, $contribution->code, $contribution->comment, $contribution->compulsory);
             array_push($paid_debts, $contributedItem);
         }

          return array_merge( $paid_debts, $reg);

    }


    public function getContributionsByItemAndSession($item, $start_quarter, $end_quarter, $session_id){
        return DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'user_contributions.session_id')
            ->where('payment_items.id', $item)
            ->where('user_contributions.approve', PaymentStatus::APPROVED)
            ->where('sessions.id', $session_id)
            ->whereBetween('user_contributions.created_at', [$start_quarter, $end_quarter])
            ->selectRaw('SUM(user_contributions.amount_deposited) as amount, sessions.year as name, sessions.id, sessions.year')
            ->get()->toArray();
    }


    public function getContributionsByItemAndYear($item, $year){
        return DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'user_contributions.session_id')
            ->where('payment_items.id', $item)
            ->where('user_contributions.approve', PaymentStatus::APPROVED)
            ->where('user_contributions.session_id', $year)
            ->selectRaw('SUM(user_contributions.amount_deposited) as amount, sessions.year as name, sessions.id, sessions.year')
            ->get()->toArray();
    }


    public function getApproveMembersContributionPerActivity($id)
    {
        return DB::table('user_contributions')
            ->join('users', 'users.id', '=' ,'user_contributions.user_id')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->where('user_contributions.payment_item_id', $id)
            ->where('user_contributions.approve', PaymentStatus::APPROVED)
            ->selectRaw('SUM(user_contributions.amount_deposited) as amount, payment_items.name')
            ->groupBy('user_contributions.payment_item_id')
            ->get();
    }


    public function getYearlyContributions($request)
    {
        $data = DB::table('user_contributions')
                    ->join('sessions', 'sessions.id', '=', 'user_contributions.session_id')
                    ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
                    ->where('sessions.id', $request->session_id)
                    ->where('user_contributions.approve', PaymentStatus::APPROVED)
                    ->select('user_contributions.*')
                    ->get()->toArray();
        return collect($data)->sum('amount_deposited');
    }

    public function getContributionStatistics($request)
    {
        $payment_items =  $this->paymentItemService->getPaymentItemsBySessionAndFrequency($request);
        $percentage_contributions = $this->getPercentageContributionsByItemAndSession($payment_items, $request->session_id);
        $average_contributions_by_frequency = $this->getAverageContributionsByPaymentFrequency($request);
        $average_contributions_by_type = $this->getAverageContributionByPaymentItemType($request);

         return [["percentages_data" =>$percentage_contributions], ["avg_by_frequency" => $average_contributions_by_frequency], ["avg_by_type" => $average_contributions_by_type]];
    }

    private function getMemberRegistration($user_id)
    {
        $reg_debts = [];
        $reg = Registration::where('is_compulsory', true)->first();
        $sessions = $this->sessionService->getAllSessions();
        if(isset($reg)){
            if($reg->frequency == RegistrationFrequency::YEARLY){
                foreach ($sessions as $session){
                    $reg_session = DB::table('member_registrations')
                        ->join('users', 'users.id', '=', 'member_registrations.user_id')
                        ->join('sessions', 'sessions.id', '=', 'member_registrations.session_id')
                        ->where('member_registrations.user_id', $user_id)
                        ->where('member_registrations.session_id', $session->id)
                        ->whereIn('member_registrations.approve', [PaymentStatus::PENDING, PaymentStatus::APPROVED])
                        ->select('users.id as user_id', 'sessions.*')->first();
                    if (is_null($reg_session)){
                        $session_resource = new SessionResource($session);
                        array_push($reg_debts, new MemberPaymentItemResource($reg->id, 'Registration',
                            $reg->amount, $reg->amount, $reg->is_compulsory, null, $reg->frequency, 'REGISTRATION', $session_resource, null, null));
                    }
                }
            }
            if($reg->frequency == RegistrationFrequency::MONTHLY) {
                foreach ($this->getMonths() as $month){
                    $reg_session = DB::table('member_registrations')
                        ->join('users', 'users.id', '=', 'member_registrations.user_id')
                        ->where('member_registrations.user_id', $user_id)
                        ->where('member_registrations.month_name', $month)
                        ->whereIn('member_registrations.approve', [PaymentStatus::PENDING, PaymentStatus::APPROVED])
                        ->select( 'member_registrations.*')->first();
                    if (is_null($reg_session)){
                        array_push($reg_debts, new MemberPaymentItemResource($reg->id, 'Registration',
                            $reg->amount, $reg->amount,  $reg->is_compulsory, null, $reg->frequency, 'REGISTRATION', null, $month, null));
                    }
                }
            }
        }
        return $reg_debts;
    }

    private function findUser($id)
    {
        return User::findOrFail($id);
    }

    private function findPaymentItem($payment_item)
    {
        return PaymentItem::findOrFail($payment_item);
    }

    private function getUserContributionStatus($payment_item_amount, $total_amount_contributed)
    {
        $status = null;
        if($payment_item_amount == $total_amount_contributed){
            $status = PaymentStatus::COMPLETE;
        }else{
            $status = PaymentStatus::INCOMPLETE;
        }
        return $status;
    }

    private function findUserContributionById($id)
    {
        return UserContribution::findOrFail($id);
    }

    private function saveContribution($request, $user_id, $auth_user, $current_session)
    {

        $payment_item = $this->findPaymentItem($request->payment_item_id);

        $payment_item_amount = $payment_item->amount;

        $total_amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_id, $request->payment_item_id, $request->month_name, $request->quarterly_name, $payment_item->frequency);

        $this->validateAmountDeposited($payment_item_amount, ($total_amount_contributed + $request->amount_deposited));

        $status = $this->getUserContributionStatus($payment_item_amount, ($total_amount_contributed + $request->amount_deposited));

        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $payment_item_amount);

        $user_last_contribution = $this->getUserLastContributionByPaymentItem($user_id, $request->payment_item_id, $request->month_name, $request->quarterly_name, $payment_item->frequency);

        $balance_contribution = $user_last_contribution == null ? ($payment_item_amount- $request->amount_deposited) : ($user_last_contribution->balance - $request->amount_deposited);

        if(!$hasCompleted){
            UserContribution::create([
                'code'              => $this->generateCode(10),
                'amount_deposited'  => $request->amount_deposited,
                'comment'           => $request->comment,
                'user_id'           => $request->user_id,
                'payment_item_id'   => $payment_item->id,
                'status'            => $status,
                'scan_picture'      => null,
                'updated_by'        => $auth_user,
                'balance'           => $balance_contribution,
                'session_id'        => $current_session,
                'quarterly_name'    => !is_null($request->quarterly_name) ? ($request->quarterly_name) :"",
                'month_name'        => $request->month_name
            ]);
        }

        if($request->contributed_via_saving){
            $this->userSavingService->deductSavingAfterContribution($user_id, $request->amount_deposited);
        }

    }

    private function verifyExcessUserContribution($total_amount_contributed, $payment_item_amount)
    {
        return $total_amount_contributed == $payment_item_amount;
    }

    private function validateAmountDeposited($payment_item_amount, $amount_deposited) {
        if($amount_deposited > $payment_item_amount) {
            throw new BusinessValidationException("Amount Deposited must not be more than the amount for the payment item", 403);
        }
        return true;
    }

    private function getUserLastContributionByPaymentItem($user_id, $payment_item_id , $month_name, $quarterly_name, $frequency) {
        $contributions =  $this->getContributionByUserAndPaymentItem($payment_item_id, $user_id);
        if($frequency == PaymentItemFrequency::QUARTERLY){
            $contributions = $contributions->where('quarterly_name', $quarterly_name);
        }
        if($frequency == PaymentItemFrequency::MONTHLY){
            $contributions = $contributions->where('month_name', $month_name);
        }
        return $contributions->orderBy('user_contributions.created_at', 'DESC')->first();
    }

    private function  getUserContributions($request) {
        $contributions =  DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id' ,'=', 'user_contributions.payment_item_id')
            ->join('users', 'users.id', '=', 'user_contributions.user_id')
            ->join('sessions', 'sessions.id', '=', 'user_contributions.session_id')
            ->where('user_contributions.payment_item_id', $request->payment_item_id)
            ->where('user_contributions.session_id', $request->year);

        if(!is_null($request->status) && $request->status != "ALL"){
            if($request->status == "PENDING" || $request->status == "APPROVED" || $request->status == "DECLINED"){
                $contributions = $contributions->where('user_contributions.approve', $request->status);
            }else {
                $contributions = $contributions->where('user_contributions.status', $request->status);
            }
        }
        if(!is_null($request->month)){
            $contributions = $contributions->whereMonth('user_contributions.created_at', $request->month);
        }
        if(!is_null($request->date)){
            $contributions = $contributions->whereDate('user_contributions.created_at', $request->date);
        }
        return $contributions;
    }

    private function getContributionByUserAndPaymentItem($payment_item_id, $user_id) {
        return UserContribution::join('users', ['users.id' => 'user_contributions.user_id'])
            ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
            ->where('users.id', $user_id)
            ->where('payment_items.id', $payment_item_id)
            ->whereIn('user_contributions.approve', [PaymentStatus::APPROVED, PaymentStatus::PENDING]);
    }

    private function getMemberOwingItems($user_id, $current_session_id)
    {
        $debts = [];
        $payment_items =  PaymentItem::where('compulsory', true)->where('session_id', $current_session_id)->get();

        foreach ($payment_items as $item) {
            if ($item->type == PaymentItemType::ALL_MEMBERS){
                switch ($item->frequency) {
                    case PaymentItemFrequency::YEARLY:

                        $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session_id));
                        break;
                    case PaymentItemFrequency::QUARTERLY:
                        $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session_id));
                        break;
                    case PaymentItemFrequency::MONTHLY:
                        $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session_id));
                        break;
                    case PaymentItemFrequency::ONE_TIME:
                        $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session_id));
                        break;
                }
            }
            if ($item->type == PaymentItemType::GROUPED_MEMBERS || $item->type == PaymentItemType::A_MEMBER){
                if($this->checkMemberExistAsReference($user_id, $item->reference)){
                    switch ($item->frequency){
                        case PaymentItemFrequency::YEARLY:
                            $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::QUARTERLY:
                            $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::MONTHLY:
                            $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::ONE_TIME:
                            $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session_id));
                            break;
                    }
                }
            }

            if ($item->type == PaymentItemType::MEMBERS_WITH_ROLES){
                if (!is_null($this->checkMemberIsAdministrator($user_id))){
                    switch ($item->frequency){
                        case PaymentItemFrequency::YEARLY:
                            $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::QUARTERLY:
                            $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::MONTHLY:
                            $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::ONE_TIME:
                            $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session_id));
                            break;
                    }
                }
            }

            if ($item->type == PaymentItemType::MEMBERS_WITHOUT_ROLES){
                if (!is_null($this->checkMemberNotAdministrator($user_id))){
                    switch ($item->frequency){
                        case PaymentItemFrequency::YEARLY:
                            $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::QUARTERLY:
                            $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::MONTHLY:
                            $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session_id));
                            break;
                        case PaymentItemFrequency::ONE_TIME:
                            $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session_id));
                            break;
                    }
                }
            }
        }
        return $debts;
    }

    function mapPaymentItemId($items){
        return $items->id;
    }

    private function saveRegistration($request, $user, $auth_user)
    {
        $registration_fee = Registration::findOrFail($request->registration_id);
        $exist_user = MemberRegistration::where('user_id', $user->id)->where('session_id', $request->year)->first();
        if(is_null($exist_user)){
            MemberRegistration::create([
                'user_id'           => $user->id,
                'session_id'        => $request->year,
                'updated_by'        => $auth_user,
                'month_name'        => $request->month_name,
                'registration_id'   => $registration_fee->id
            ]);
        }else {
            $exist_user->approve = PaymentStatus::PENDING;
            $exist_user->save();
        }
    }

    private function getAllMemberContribution($user_id, $year) {
        return DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->join('users', 'users.id', '=', 'user_contributions.user_id')
            ->join('sessions', 'sessions.id', '=', 'user_contributions.session_id')
            ->where('user_contributions.user_id', $user_id)
            ->where('user_contributions.session_id', $year)
            ->select('payment_items.id as payment_item_id', 'payment_items.name','payment_items.compulsory','payment_items.amount as payment_item_amount',
                'payment_items.description','payment_items.type','payment_items.frequency','payment_items.payment_category_id',
                'payment_items.updated_at', 'user_contributions.*', 'sessions.year')
            ->orderBy('user_contributions.created_at', 'DESC')->get()->toArray();
    }

    private function getRegistration($user_id) {
        $registrations = [];
        $reg = DB::table('member_registrations')
            ->join('users', 'users.id', '=', 'member_registrations.user_id')
            ->join('sessions', 'sessions.id', '=', 'member_registrations.session_id')
            ->join('registrations', 'registrations.id', '=', 'member_registrations.registration_id')
            ->where('member_registrations.user_id', $user_id)
            ->whereIn('member_registrations.approve', [PaymentStatus::PENDING, PaymentStatus::APPROVED])
            ->select('sessions.year as year', 'member_registrations.*', 'registrations.amount', 'registrations.frequency','registrations.is_compulsory')
            ->orderBy('member_registrations.created_at', 'DESC')->get();
        foreach ($reg as $value){
           array_push($registrations,  new MemberContributedItemResource($value->id, $value->registration_id,   $value->amount, "Registration", $value->amount,0.0,
               PaymentStatus::COMPLETE, $value->approve, $value->created_at, $value->year, $value->frequency, null, null, $value->updated_by, null, "", $value->is_compulsory));
        }
        return $registrations;
    }

    private function getMonths(){
        return $months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ];
    }

    private function fetchUserContributions($payment_item_id, $user_id)
    {
        return DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->join('users', 'users.id', '=', 'user_contributions.user_id')
            ->join('sessions', 'sessions.id', '=', 'user_contributions.session_id')
            ->where('payment_items.id', $payment_item_id)
            ->where('users.id', $user_id);
    }

    private function verifyCompleteItemPaymentByYear($payment_item_id, $user_id, $session_id)
    {
        return $this->fetchUserContributions($payment_item_id, $user_id)
                    ->where('sessions.id', $session_id)
                    ->select('user_contributions.*')
                    ->get();

    }

    private function verifyIncompleteItemPaymentsByYear($payment_item_id, $user_id, $session_id)
    {
        return   $this->fetchUserContributions($payment_item_id, $user_id)
                    ->where('sessions.id', $session_id)
                    ->select('user_contributions.*')
                    ->latest()
                    ->first();
    }

    private function verifyIncompleteQuarterItemPayment($payment_item_id, $user_id, $quarter)
    {
        return $this->fetchUserContributions($payment_item_id, $user_id)
                    ->where('user_contributions.quarterly_name', $quarter)
                    ->select('user_contributions.*')
                    ->latest()
                    ->first();
    }

    private function verifyCompleteItemPaymentByQuarter($payment_item_id, $user_id, $quarter)
    {
        return $this->fetchUserContributions($payment_item_id, $user_id)
            ->where('user_contributions.quarterly_name', $quarter)
            ->select('user_contributions.*')
            ->get();

    }

    private function verifyCompleteItemPaymentByMonth($payment_item_id, $user_id, $month)
    {
        return $this->fetchUserContributions($payment_item_id, $user_id)
            ->where('user_contributions.month_name', $month)
            ->select('user_contributions.*')
            ->get();

    }

    private function verifyIncompleteMonthItemPayment($payment_item_id, $user_id, $month)
    {
        return $this->fetchUserContributions($payment_item_id, $user_id)
            ->where('user_contributions.month_name', $month)
            ->select('user_contributions.*')
            ->latest()
            ->first();
    }

    private function verifyOneTimeCompleteItemPayment($payment_item_id, $user_id)
    {
        return $this->fetchUserContributions($payment_item_id, $user_id)
            ->select('user_contributions.*')
            ->get();
    }

    private function verifyOneTimeIncompleteItemPayment($payment_item_id, $user_id)
    {
        return $this->fetchUserContributions($payment_item_id, $user_id)
            ->select('user_contributions.*')
            ->latest()
            ->first();
    }

    private function verifyItemPaymentByYear($item, $user_id, $current_session) {
        $debts = [];
        if(count($this->verifyCompleteItemPaymentByYear($item->id, $user_id, $current_session)) == 0){
             $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$current_session, null, null );
            array_push($debts, $to_be_paid);
        }
        $last_payment = $this->verifyIncompleteItemPaymentsByYear($item->id, $user_id, $current_session);
        if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
             $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION", $current_session, null, null );
            array_push($debts, $to_be_paid);
        }

        return $debts;
    }

    private function verifyQuarterlyPayment($item, $user_id, $current_session)
    {
        $debts = [];
        $quarters = $this->getPaymentItemQuartersBySession($item->frequency, $item->created_at);
        foreach ($quarters as $quarter){
            if (count($this->verifyCompleteItemPaymentByQuarter($item->id, $user_id, $quarter)) == 0) {
                 $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                    $item->type, $item->frequency,"CONTRIBUTION",$current_session, null, $quarter );
                array_push($debts, $to_be_paid);
            }
            $last_payment = $this->verifyIncompleteQuarterItemPayment($item->id, $user_id, $quarter);
            if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
                 $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance, $item->amount, $item->compulsory,
                    $item->type, $item->frequency,"CONTRIBUTION",$current_session, null, $quarter );
                array_push($debts, $to_be_paid);
            }
        }
        return $debts;
    }

    private function verifyMonthlyPayment($item, $user_id, $current_session)
    {
        $debts = [];
        $month_list = $this->getPaymentItemMonthsBySession($item->frequency, $item->created_at);
        foreach ($month_list as $month){
            if (count($this->verifyCompleteItemPaymentByMonth($item->id, $user_id, $month)) == 0) {
                 $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                    $item->type, $item->frequency,"CONTRIBUTION",$current_session, $month, null );
                array_push($debts, $to_be_paid);
            }
            $last_payment = $this->verifyIncompleteMonthItemPayment($item->id, $user_id, $month);
            if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
                $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance,$item->amount, $item->compulsory,
                    $item->type, $item->frequency,"CONTRIBUTION",$current_session, $month, null );
                array_push($debts, $to_be_paid);
            }
        }

        return $debts;
    }

    public function getPaymentItemMonthsBySession($item_frequency, $item_created_at)
    {
        $all_months = $this->getMonths();
        $current_month_index = $this->getItemMonth($item_frequency, $item_created_at)->month;
        return array_splice($all_months, $current_month_index - 1, count($all_months));
    }


    private function verifyOneTimePayment($item, $user_id, $current_session)
    {
        $debts = [];
        if (count($this->verifyOneTimeCompleteItemPayment($item->id, $user_id)) == 0) {
             $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$current_session, null, null );
            array_push($debts, $to_be_paid);
        }
        $last_payment = $this->verifyOneTimeIncompleteItemPayment($item->id, $user_id);
        if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
             $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$current_session,  null, null );
            array_push($debts, $to_be_paid);
        }


        return $debts;
    }

    public function getPercentageContributionsByItemAndSession($payment_items, $session_id)
    {
        $percentages = [];
        foreach ($payment_items as $payment_item){
            $contributions = DB::table('user_contributions')
                ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
                ->join('sessions', 'sessions.id', '=', 'user_contributions.session_id')
                ->where('user_contributions.session_id', $session_id)
                ->where('user_contributions.payment_item_id', $payment_item->id)
                ->where('user_contributions.approve', PaymentStatus::APPROVED)
                ->select('user_contributions.*')
                ->orderBy('payment_items.created_at')
                ->get()
                ->toArray();
            $totalContribution = collect($contributions)->sum('amount_deposited');
            $contributors = count($contributions);
            array_push($percentages, ["name" => $payment_item->name, "percentage" => $totalContribution, "contributors" => $contributors]);
        }

        return $percentages;
    }

    public function getAverageContributionsByPaymentFrequency($request)
    {
        $frequencies = [PaymentItemFrequency::YEARLY, PaymentItemFrequency::QUARTERLY, PaymentItemFrequency::MONTHLY, PaymentItemFrequency::ONE_TIME];
        $averages = [];
        foreach ($frequencies as $frequency){
            switch ($frequency){
                case  PaymentItemFrequency::YEARLY:
                    $payment_items = $this->paymentItemService->getPaymentItemsByFrequency($request->session_id, PaymentItemFrequency::YEARLY);
                    $averages[] = $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id);
                    break;
                case  PaymentItemFrequency::QUARTERLY:
                    $payment_items = $this->paymentItemService->getPaymentItemsByFrequency($request->session_id, PaymentItemFrequency::QUARTERLY);
                    $averages[] = $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id);
                    break;
                case  PaymentItemFrequency::MONTHLY:
                    $payment_items = $this->paymentItemService->getPaymentItemsByFrequency($request->session_id, PaymentItemFrequency::MONTHLY);
                    $averages[] = $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id);
                    break;
                case  PaymentItemFrequency::ONE_TIME:
                    $payment_items = $this->paymentItemService->getPaymentItemsByFrequency($request->session_id, PaymentItemFrequency::ONE_TIME);
                    $averages[] = $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id);
                    break;
            }
        }

        return $averages;
    }

    private function getAverageContributionByPaymentItemType($request)
    {
        $frequencies = [PaymentItemType::ALL_MEMBERS, PaymentItemType::A_MEMBER, PaymentItemType::MEMBERS_WITH_ROLES, PaymentItemType::MEMBERS_WITHOUT_ROLES, PaymentItemType::GROUPED_MEMBERS];
        $averages = [];
        foreach ($frequencies as $frequency){
            switch ($frequency){
                case  PaymentItemType::ALL_MEMBERS:
                    $payment_items = $this->paymentItemService->getPaymentItemsByType($request->session_id, PaymentItemType::ALL_MEMBERS);
                    array_push($averages, $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id));
                    break;
                case  PaymentItemType::A_MEMBER:
                    $payment_items = $this->paymentItemService->getPaymentItemsByType($request->session_id, PaymentItemType::A_MEMBER);
                    array_push($averages, $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id));
                    break;
                case  PaymentItemType::MEMBERS_WITH_ROLES:
                    $payment_items = $this->paymentItemService->getPaymentItemsByType($request->session_id, PaymentItemType::MEMBERS_WITH_ROLES);
                    array_push($averages, $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id));
                    break;
                case  PaymentItemType::MEMBERS_WITHOUT_ROLES:
                    $payment_items = $this->paymentItemService->getPaymentItemsByType($request->session_id, PaymentItemType::MEMBERS_WITHOUT_ROLES);
                    array_push($averages, $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id));
                    break;
                case  PaymentItemType::GROUPED_MEMBERS:
                    $payment_items = $this->paymentItemService->getPaymentItemsByType($request->session_id, PaymentItemType::GROUPED_MEMBERS);
                    array_push($averages, $this->computeAverageContributionByPaymentFrequency($payment_items, $request->session_id));
                    break;
            }
        }

        return $averages;
    }

    private function computeAverageContributionByPaymentFrequency($payment_items, $session_id)
    {
        $contributions_percentages = $this->getPercentageContributionsByItemAndSession($payment_items, $session_id);

        return count($payment_items) > 0 ? round(collect($contributions_percentages)->sum('percentage') / count($payment_items), 2): 0;

    }

    private function getPaidQuartersName($contributions)
    {
        return collect($contributions)->map(function ($contribution) {
            return $contribution->quarterly_name;
        })->toArray();
    }

    private function getPaidMonthNames($contributions)
    {
        return collect($contributions)->map(function ($contribution) {
            return $contribution->month_name;
        })->toArray();
    }


    private function getMemberUnPayQuarters($payment_item_frequency, $payment_item_created_at, $contributions)
    {
        $payable_quarters = $this->getPaymentItemQuartersBySession($payment_item_frequency, $payment_item_created_at);
        $paid_quarters = $this->getPaidQuartersName($contributions);
        return collect($payable_quarters)->filter(function ($quarter) use ($paid_quarters){
            return !in_array($quarter, $paid_quarters);
        })->toArray();
    }

    private function getMemberUnPayMonths($payment_item_frequency, $payment_item_created_at, $contributions)
    {
        $payable_months = $this->getPaymentItemMonthsBySession($payment_item_frequency, $payment_item_created_at);
        $paid_months = $this->getPaidMonthNames($contributions);
        return collect($payable_months)->filter(function ($month) use ($paid_months){
            return !in_array($month, $paid_months);
        })->toArray();
    }
}

