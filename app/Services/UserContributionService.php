<?php

namespace App\Services;

use App\Constants\PaymentItemType;
use App\Constants\SessionStatus;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\MemberContributedItemResource;
use App\Http\Resources\MemberPaymentItemResource;
use App\Interfaces\UserContributionInterface;
use App\Models\MemberRegistration;
use App\Models\PaymentItem;
use App\Models\User;
use App\Models\UserContribution;
use App\Traits\HelpTrait;
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

    public function approveUserContribution($id, $type)
    {
        $user_contribution =  $this->findUserContributionById($id);
        $user_contribution->approve = $type;
        $user_contribution->save();
    }


    public function filterContributions($request)
    {
        $contributions = $this->getUserContributions($request);
        $contributions = $contributions->selectRaw('SUM(user_contributions.amount_deposited) as total_amount_deposited, user_contributions.*, sessions.*')
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
        $reg_debts = $this->checkIfMemberIsRegistered($user_id, $year);
        $debts = $this->getMemberOwingItems($year, $user_id);
        if(!is_null($reg_debts)){
            array_push($debts, $reg_debts);
        }
        return $debts;
    }


    public function getMemberContributedItems($user_id, $year)
    {
        $paid_debts = [];
         $reg = $this->getRegistration($user_id, $year);
         $contributions = $this->getAllMemberContribution($user_id, $year);
         foreach ($contributions as $contribution){
             $contributedItem = new MemberContributedItemResource($contribution->id, $contribution->payment_item_id, $contribution->name,
                 $contribution->amount_deposited, $contribution->balance, $contribution->status, $contribution->approve, $contribution->created_at, $year);
             array_push($paid_debts, $contributedItem);
         }
         if(!is_null($reg)){
             array_push( $paid_debts, $reg);
         }
         return $paid_debts;

    }

    private function getMemberRegistration($user_id, $year)
    {
        return DB::table('payment_items')
            ->leftJoin('member_registrations', 'member_registrations.payment_item_id', '=', 'payment_items.id')
            ->leftJoin('users', 'users.id', '=', 'member_registrations.user_id')
            ->where('member_registrations.user_id', $user_id)
            ->where('member_registrations.year', $year);
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
                'session_id'        => $current_session->id
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

    private function checkIfMemberIsRegistered($user_id, $year)
    {
        $payment_item = null;
        $exist = $this->getMemberRegistration($user_id, $year)
                 ->whereIn('member_registrations.approve', [PaymentStatus::APPROVED, PaymentStatus::PENDING])
                 ->first();
        if(is_null($exist)){
            $payment_item = DB::table('registrations')->where('status', SessionStatus::ACTIVE)->where('is_compulsory', true)->first();
            //need to add check for freq
            $payment_item =  new MemberPaymentItemResource($payment_item->id,
                "Registration Fee", $payment_item->amount, $payment_item->compulsory, null, $payment_item->frequency, 'REGISTRATION');
        }
        return $payment_item;
    }

    private function getMemberOwingItems($year, $user_id)
    {
        $debts = [];
        $contributions = $this->getAllMemberContribution($user_id, $year);
        $items =  DB::table('payment_items')->where('compulsory', true)->where('type', PaymentItemType::NORMAL)->select('*')->get()->collect();

        if(count($contributions) == 0) {
            foreach ($items as $item){
                array_push($debts, new MemberPaymentItemResource($item->id, $item->name, $item->amount, $item->complusory, $item->type, $item->frequency, 'CONTRIBUTION'));
            }
        }else {
            $payment_item_ids = $items->map(function ($item) {
                return $item->id;
            });
            foreach ($contributions as $contribution){
                $total_amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_id, $contribution->payment_item_id);
                if(!in_array($contribution->payment_item_id, $payment_item_ids->toArray())){
                    array_push($debts, new MemberPaymentItemResource($contribution->payment_item_id, $contribution->name,
                        $contribution->payment_item_amount, $contribution->complusory, $contribution->type, $contribution->frequency, 'CONTRIBUTION'));
                }
                if(in_array($contribution->payment_item_id, $payment_item_ids->toArray()) && $contribution->payment_item_amount != $total_amount_contributed){
                    $balance_payment = $contribution->payment_item_amount - $total_amount_contributed;
                    array_push($debts, new MemberPaymentItemResource($contribution->payment_item_id, $contribution->name,
                        $balance_payment, $contribution->complusory, $contribution->type, $contribution->frequency, 'CONTRIBUTION'));

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
        $payment_item = PaymentItem::findOrFail($request->payment_item_id);
        $exist_user = MemberRegistration::where('user_id', $user->id)->where('year', $request->year)->get()->toArray();
        if(count($exist_user) == 0){
            MemberRegistration::create([
                'user_id'           => $user->id,
                'year'              => $request->year,
                'payment_item_id'   => $payment_item->id,
                'updated_by'        => $auth_user
            ]);
        }else {
            $exist_user[0]->approve = PaymentStatus::PENDING;
            $exist_user[0]->save();
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

    private function getRegistration($user_id, $year) {
        $registration = null;
        $data = $this->getMemberRegistration($user_id, $year)
            ->whereIn('member_registrations.approve', [PaymentStatus::APPROVED, PaymentStatus::PENDING])
            ->select('payment_items.id as payment_item_id', 'payment_items.name', 'payment_items.amount', 'member_registrations.*')->first();
        if(!is_null($data)){
            $registration = new MemberContributedItemResource($data->id, $data->payment_item_id, $data->name,  $data->amount, 0.0, PaymentStatus::COMPLETE, $data->approve, $data->created_at, $data->year);
        }

        return $registration;
    }




}

