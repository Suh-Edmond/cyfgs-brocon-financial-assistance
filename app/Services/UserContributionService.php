<?php

namespace App\Services;

use App\Constants\PaymentItemFrequency;
use App\Constants\PaymentItemType;
use App\Constants\RegistrationFrequency;
use App\Constants\SessionStatus;
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
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\DB;
use App\Constants\PaymentStatus;
use App\Http\Resources\UserContributionCollection;


class UserContributionService implements UserContributionInterface {

    use HelpTrait;
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function createUserContribution($request)
    {
        $this->saveContribution($request, $request->user_id, $request->user()->name);

    }

    public function updateUserContribution($request, $id)
    {
        $user_contribution = $this->findUserContributionById($id);

        $amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_contribution->user_id, $user_contribution->payment_item_id) - $user_contribution->amount_deposited;

        $total_amount_contributed = $amount_contributed + $request->amount_deposited;

        $balance = $user_contribution->paymentItem->amount - $total_amount_contributed;

        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $user_contribution->paymentItem->amount);

        $status = $this->getUserContributionStatus($user_contribution->paymentItem->amount, $total_amount_contributed);

        if(!$hasCompleted){
            if($user_contribution->approve == PaymentStatus::PENDING){
                $user_contribution->update([
                    'amount_deposited' => $request->amount_deposited,
                    'comment'          => $request->comment,
                    'scan_picture'     => $request->scan_picture,
                    'balance'          => $balance,
                    'status'           => $status
                ]);
            }else {
                throw new BusinessValidationException("Income activity cannot be updated after been approved or declined");
            }
        }
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

    public function getContributionByUserAndItem($payment_item_id, $user_id)
    {
        return $this->getContributionByUserAndPaymentItem($payment_item_id, $user_id)
                                    ->select(  'user_contributions.*')
                                    ->orderBy('user_contributions.created_at', 'DESC')
                                    ->get();
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
        $contributions = $this->getUserContributions($request);
        $contributions = $contributions->selectRaw('SUM(user_contributions.amount_deposited) as total_amount_deposited, user_contributions.*')
            ->groupBy('user_contributions.user_id')->orderBy('user_contributions.created_at', 'DESC')->get();
        return  $contributions;
    }

    public function getContribution($id)
    {
        return $this->findUserContributionById($id);
    }


    public function getTotalBalanceByUserAndItem($payment_item_amount, $total)
    {
        return ($payment_item_amount - $total);
    }


    public function getTotalAmountPaidByUserForTheItem($user_id, $payment_item_id)
    {
        return $this->getContributionByUserAndPaymentItem($payment_item_id, $user_id)->sum('user_contributions.amount_deposited');
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
        $total = 0;
        $user_contributions = UserContribution::join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                              ->where('payment_items.id', $payment_item_id)
                              ->where('user_contributions.session_id', $year)
                              ->orderBy('payment_items.name', 'ASC')
                              ->get();
        if(isset($user_contributions)){
            foreach($user_contributions as $user_contribution){
                $total += $user_contribution->amount_deposited;
            }
        }

        return new UserContributionCollection($user_contributions, $total);
    }
    public function bulkPayment($request)
    {
        $auth_user = $request->user()->name;
        $payload = $request->all();
        $user = User::findOrFail(json_decode(json_encode($payload[0]))->user_id);
       foreach ($payload as $value){
           $json_data = json_decode(json_encode($value));
            if($json_data->code == 'REGISTRATION'){
                $this->saveRegistration($json_data, $user, $auth_user);
            } elseif ($json_data->code == 'CONTRIBUTION'){
                $this->saveContribution($json_data, $user->id, $auth_user);
            }
        }
    }

    public function getMemberDebt($user_id, $year)
    {
        $reg_debts = $this->getMemberRegistration($user_id);
        $debts = $this->getMemberOwingItems($user_id);
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
                 $contribution->amount_deposited, $contribution->balance, $contribution->status, $contribution->approve, $contribution->created_at, $year);
             array_push($paid_debts, $contributedItem);
         }

          return array_merge( $paid_debts, $reg);

    }

    private function getMemberRegistration($user_id)
    {
        $reg_debts = [];
        $reg = Registration::where('is_compulsory', true)->first();
        $sessions = $this->sessionService->getAllSessions();
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
                    array_push($reg_debts, new MemberPaymentItemResource($reg->id, 'Members Registration',
                        $reg->amount, $reg->amount, $reg->is_compulsory, null, $reg->frequency, 'REGISTRATION', $session_resource, null, $this->getDateQuarter()));
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
                    array_push($reg_debts, new MemberPaymentItemResource($reg->id, 'Members Registration',
                        $reg->amount, $reg->amount,  $reg->is_compulsory, null, $reg->frequency, 'REGISTRATION', null, $month, $this->getDateQuarter()));
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

    private function saveContribution($request, $user_id, $auth_user)
    {
        $current_session = $this->sessionService->getCurrentSession();

        $payment_item = $this->findPaymentItem($request->payment_item_id);

        $payment_item_amount = $payment_item->amount;

        $total_amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_id, $request->payment_item_id);

        $this->validateAmountDeposited($payment_item_amount, ($total_amount_contributed + $request->amount_deposited));

        $status = $this->getUserContributionStatus($payment_item_amount, ($total_amount_contributed + $request->amount_deposited));

        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $payment_item_amount);

        $user_last_contribution = $this->getUserLastContributionByPaymentItem($user_id, $request->payment_item_id);

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
                'session_id'        => $current_session->id,
                'quarterly_name'    => !is_null($request->quarterly_name) ? $this->convertQuarterNameToNumber($request->quarterly_name) :"",
                'month_name'        => $request->month_name
            ]);
        }

    }

    private function verifyExcessUserContribution($total_amount_contributed, $payment_item_amount)
    {
        return $total_amount_contributed == $payment_item_amount;
    }

    private function validateAmountDeposited($payment_item_amount, $amount_deposited) {
        if($amount_deposited > $payment_item_amount) {
            throw new BusinessValidationException("Amount Deposited must not be more than the amount for the payment item");
        }

        return true;
    }

    private function getUserLastContributionByPaymentItem($user_id, $payment_item_id) {
        return $this->getContributionByUserAndPaymentItem($payment_item_id, $user_id)->orderBy('user_contributions.created_at', 'DESC')->first();
    }

    private function  getUserContributions($request) {
        $contributions =  DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id' ,'=', 'user_contributions.payment_item_id')
            ->join('users', 'users.id', '=', 'user_contributions.user_id')
            ->join('sessions', 'sessions.id', '=', 'user_contributions.session_id')
            ->where('user_contributions.payment_item_id', $request->payment_item_id)
            ->where('user_contributions.session_id', $request->year);

        if(!is_null($request->status) && $request->status != "ALL"){
            if($request->status == "PENDING" || "APPROVED" || "DECLINED"){
                $contributions = $contributions->where('user_contributions.approve', $request->status);
            }else {
                $contributions = $contributions->where('user_contributions.status', $request->status);
            }
        }
        if(!is_null($request->month)){
            $contributions = $contributions->whereMonth('user_contributions.created_at', $this->convertMonthNameToNumber($request->month));
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
            ->where('payment_items.id', $payment_item_id);
    }

    private function getMemberOwingItems($user_id)
    {
        $debts = [];
        $current_session = $this->sessionService->getCurrentSession();
        $payment_items =  PaymentItem::where('compulsory', true)->where('session_id', $current_session->id)->get();
        foreach ($payment_items as $item) {
            if ($item->type == PaymentItemType::ALL_MEMBERS){
                switch ($item->frequency) {
                    case PaymentItemFrequency::YEARLY:
                        $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session));
                        break;
                    case PaymentItemFrequency::QUARTERLY:
                        $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session));
                        break;
                    case PaymentItemFrequency::MONTHLY:
                        $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session));
                        break;
                    case PaymentItemFrequency::ONE_TIME:
                        $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session));
                        break;
                }
            }
            if ($item->type == PaymentItemType::GROUPED_MEMBERS || PaymentItemType::A_MEMBER){
                if($this->checkMemberExistAsReference($user_id, $item->reference)){
                    switch ($item->frequency){
                        case PaymentItemFrequency::YEARLY:
                            $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::QUARTERLY:
                            $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::MONTHLY:
                            $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::ONE_TIME:
                            $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session));
                            break;
                    }
                }
            }
            if ($item->type == PaymentItemType::MEMBERS_WITH_ROLES){
                if (!is_null($this->checkMemberIsAdministrator($user_id))){
                    switch ($item->frequency){
                        case PaymentItemFrequency::YEARLY:
                            $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::QUARTERLY:
                            $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::MONTHLY:
                            $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::ONE_TIME:
                            $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session));
                            break;
                    }
                }
            }
            if ($item->type == PaymentItemType::MEMBERS_WITHOUT_ROLES){
                if (!is_null($this->checkMemberNotAdministrator($user_id))){
                    switch ($item->frequency){
                        case PaymentItemFrequency::YEARLY:
                            $debts = array_merge($debts, $this->verifyItemPaymentByYear($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::QUARTERLY:
                            $debts = array_merge($debts, $this->verifyQuarterlyPayment($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::MONTHLY:
                            $debts = array_merge($debts, $this->verifyMonthlyPayment($item, $user_id, $current_session));
                            break;
                        case PaymentItemFrequency::ONE_TIME:
                            $debts = array_merge($debts,  $this->verifyOneTimePayment($item, $user_id, $current_session));
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
                'payment_items.created_at','payment_items.updated_at','payment_items.updated_by', 'user_contributions.*')
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
           array_push($registrations,  new MemberContributedItemResource($value->id, $value->registration_id,   $value->amount, "Member's Registration", $value->amount,0.0,
               PaymentStatus::COMPLETE, $value->approve, $value->created_at, $value->year));
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
        if(count($this->verifyCompleteItemPaymentByYear($item->id, $user_id, $current_session->id)) == 0){
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource, null, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }
        $last_payment = $this->verifyIncompleteItemPaymentsByYear($item->id, $user_id, $current_session->id);
        if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource, null, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }

        return $debts;
    }

    private function verifyQuarterlyPayment($item, $user_id, $current_session)
    {
        $debts = [];
        if (count($this->verifyCompleteItemPaymentByQuarter($item->id, $user_id, $this->getDateQuarter())) == 0) {
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource, null, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }
        $last_payment = $this->verifyIncompleteQuarterItemPayment($item->id, $user_id, $this->getDateQuarter());
        if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource, null, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }


        return $debts;
    }

    private function verifyMonthlyPayment($item, $user_id, $current_session)
    {
        $debts = [];
        $current_month = $this->convertNumberToMonth(Carbon::now()->month);

        if (count($this->verifyCompleteItemPaymentByMonth($item->id, $user_id, $current_month)) == 0) {
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource, $current_month, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }
        $last_payment = $this->verifyIncompleteMonthItemPayment($item->id, $user_id, $current_month);
        if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource, $current_month, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }


        return $debts;
    }

    private function verifyOneTimePayment($item, $user_id, $current_session)
    {
        $debts = [];
        if (count($this->verifyOneTimeCompleteItemPayment($item->id, $user_id)) == 0) {
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $item->amount,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource, null, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }
        $last_payment = $this->verifyOneTimeIncompleteItemPayment($item->id, $user_id);
        if(!is_null($last_payment) && $last_payment->status != PaymentStatus::COMPLETE){
            $session_resource = new SessionResource($current_session);
            $to_be_paid = new MemberPaymentItemResource($item->id, $item->name, $last_payment->balance,$item->amount, $item->compulsory,
                $item->type, $item->frequency,"CONTRIBUTION",$session_resource,  null, $this->getDateQuarter() );
            array_push($debts, $to_be_paid);
        }


        return $debts;
    }

    public function getContributionsByItemAndSession($item, $start_quarter, $end_quarter){
        return DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'user_contributions.session_id')
            ->where('payment_items.id', $item)
            ->where('user_contributions.approve', PaymentStatus::APPROVED)
            ->whereBetween('user_contributions.created_at', [$start_quarter, $end_quarter])
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
}

