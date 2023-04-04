<?php

namespace App\Services;

use App\Exceptions\BusinessValidationException;
use App\Http\Resources\MemberContributedItemResource;
use App\Http\Resources\MemberPaymentItemResource;
use App\Interfaces\UserContributionInterface;
use App\Models\MemberRegistration;
use App\Models\PaymentItem;
use App\Models\User;
use App\Models\UserContribution;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Constants\PaymentStatus;
use App\Http\Resources\UserContributionCollection;


class UserContributionService implements UserContributionInterface {

    use HelpTrait;

    public function createUserContribution($request)
    {
        $user = $this->findUser($request->user_id);

        $payment_item = $this->findPaymentItem($request->payment_item_id);

        $payment_item_amount = $payment_item->amount;

        $total_amount_contributed = $this->getTotalAmountPaidByUserForTheItem($request->user_id, $request->payment_item_id);

        $this->validateAmountDeposited($payment_item_amount, ($total_amount_contributed + $request->amount_deposited));

        $status = $this->getUserContributionStatus($payment_item_amount, ($total_amount_contributed + $request->amount_deposited));

        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $payment_item_amount);

        $user_last_contribution = $this->getUserLastContributionByPaymentItem($request->user_id, $request->payment_item_id);

        $balance_contribution = $user_last_contribution == null ? ($payment_item_amount- $request->amount_deposited) : ($user_last_contribution->balance - $request->amount_deposited);

        if(!$hasCompleted){
            UserContribution::create([
                'code'              => $this->generateCode(10),
                'amount_deposited'  => $request->amount_deposited,
                'comment'           => $request->comment,
                'user_id'           => $user->id,
                'payment_item_id'   => $payment_item->id,
                'status'            => $status,
                'scan_picture'      => $request->scan_picture,
                'updated_by'        => $request->user()->name,
                'balance'           => $balance_contribution
            ]);
        }

    }

    public function updateUserContribution($request, $id)
    {
        $user_contribution = $this->findUserContributionById($id);

        $total_amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_contribution->user_id, $user_contribution->payment_item_id);

        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $user_contribution->paymentItem->amount);

        $balance = $user_contribution->balance - $request->amount_deposited;

        if(!$hasCompleted){
            if($user_contribution->approve == PaymentStatus::PENDING){
                $user_contribution->update([
                    'amount_deposited' => $request->amount_deposited,
                    'comment'          => $request->comment,
                    'scan_picture'     => $request->scan_picture,
                    'balance'          => $balance,
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
        $contributions = $this->getUserContributions($request->status,$request->payment_item_id, $request->year, $request->month, $request->date);
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
                              ->whereYear('user_contributions.created_at', $year)
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
        $auth_user = User::find(Auth::id());
        $user = User::findOrFail(json_decode(json_encode($request[0]))->user_id);
       foreach ($request as $value){
           $json_data = json_decode(json_encode($value));
            if($json_data->type == 'REGISTRATION'){
                $this->saveRegistration($json_data, $user, $auth_user);
            } elseif ($json_data->type == 'CONTRIBUTION'){
                $this->saveContribution($json_data, $user->id, $auth_user);
            }
        }
    }

    public function getMemberDebt($user_id, $year)
    {
        $debts = $this->getMemberOwingItems($year, $user_id);
        $reg_debts = $this->checkIfMemberIsRegistered($user_id, $year);
        return array_merge($debts, $reg_debts);
    }


    public function getMemberContributedItems($user_id, $year)
    {
         $reg = $this->getRegistration($user_id, $year);
         $contributions = $this->getAllMemberContribution($user_id, $year);
         if(!is_null($reg)){
             array_push( $contributions, $reg);
         }
         return $contributions;

    }

    private function getMemberRegistration($user_id, $year)
    {
        return DB::table('users')
            ->leftJoin('member_registrations', 'member_registrations.user_id', '=', 'users.id')
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
        $payment_item = PaymentItem::findOrFail($request->payment_item_id);

        $total_amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_id, $request->payment_item_id);

        $this->validateAmountDeposited($payment_item->amount, ($total_amount_contributed + $request->amount_deposited));

        $status = $this->getUserContributionStatus($payment_item->amount, ($total_amount_contributed + $request->amount_deposited));

        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $payment_item->amount);

        $user_last_contribution = $this->getUserLastContributionByPaymentItem($user_id, $request->payment_item_id);

        $balance_contribution = $user_last_contribution == null ? ($payment_item->amount - $request->amount_deposited) : ($user_last_contribution->balance - $request->amount_deposited);

        if(!$hasCompleted){
            UserContribution::create([
                'code'              => $this->generateCode(10),
                'amount_deposited'  => $request->amount_deposited,
                'comment'           => $request->comment,
                'user_id'           => $user_id,
                'payment_item_id'   => $payment_item->id,
                'status'            => $status,
                'scan_picture'      => null,
                'updated_by'        => $auth_user->name,
                'balance'           => $balance_contribution
            ]);
        }

    }

    private function verifyExcessUserContribution($total_amount_contributed, $payment_item_amount)
    {
        return $total_amount_contributed == $payment_item_amount;
    }

    private function generateResponse($user_contributions, $user_id, $payment_item_id)
    {
        if(isset($user_contributions)){
            $total     = $this->getTotalAmountPaidByUserForTheItem($user_id, $payment_item_id);

            $balance   = $this->getTotalBalanceByUserAndItem($user_contributions[0]->paymentItem->amount, $total);

            $response  = new UserContributionCollection($user_contributions, $total, $balance);

        }else{
            $response = [];
        }

        return $response;
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

    private function  getUserContributions($status, $payment_item, $year, $month, $date) {
        $contributions =  DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id' ,'=', 'user_contributions.payment_item_id')
            ->join('users', 'users.id', '=', 'user_contributions.user_id')
            ->where('user_contributions.payment_item_id', $payment_item);

        if(!is_null($status) && $status != "ALL"){
            if($status == "PENDING" || "APPROVED" || "DECLINED"){
                $contributions = $contributions->where('user_contributions.approve', $status);
            }else {
                $contributions = $contributions->where('user_contributions.status', $status);
            }
        }

        if(!is_null($year)) {
            $contributions = $contributions->whereYear('user_contributions.created_at', $year);
        }

        if(!is_null($month)){
            $contributions = $contributions->whereMonth('user_contributions.created_at', $this->convertMonthNameToNumber($month));
        }

        if(!is_null($date)){
            $contributions = $contributions->whereDate('user_contributions.created_at', $date);
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
        $owing_items = [];
        $exist = $this->getMemberRegistration($user_id, $year)
                 ->whereIn('member_registrations.approve', [PaymentStatus::APPROVED, PaymentStatus::PENDING])->count();
        if($exist != 1){
            array_push($owing_items, new MemberPaymentItemResource(null, 'REGISTRATION', '500', 'Registration', false, "YES"));
        }

        return $owing_items;
    }

    private function getMemberOwingItems($year, $user_id)
    {
        $debts = [];
        $payments = DB::table('payment_items')
            ->leftJoin('user_contributions', 'user_contributions.payment_item_id', '=', 'payment_items.id')
            ->select('payment_items.*', 'user_contributions.user_id as user_id')
            ->where('payment_items.complusory', true)
            ->whereYear('payment_items.created_at', $year)
            ->orderBy('payment_items.created_at', 'DESC')->get();
        $payments = array_filter($payments->toArray(), function ($item) use ($user_id) {
            return $item->user_id != $user_id;
        });
        foreach ($payments as $value) {
            array_push($debts, new MemberPaymentItemResource($value->id, 'CONTRIBUTION', $value->amount, $value->name, false, 'YES'));
        }


        return $debts;
    }

    private function saveRegistration($request, $user, $auth_user)
    {
        MemberRegistration::create([
            'user_id'     => $user->id,
            'year'        => $request->year,
            'amount'      => $request->amount_deposited,
            'updated_by'  => $auth_user->name
        ]);
    }

    private function getAllMemberContribution($user_id, $year) {
        $paid_items = [];
        $data = DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->join('users', 'users.id', '=', 'user_contributions.user_id')
            ->where('user_contributions.user_id', $user_id)
            ->whereYear('user_contributions.created_at', $year)
            ->select('payment_items.id as payment_item_id', 'payment_items.name', 'user_contributions.*')
            ->orderBy('user_contributions.created_at', 'DESC')->get();
        foreach ($data as $key => $value ){
            array_push($paid_items, new MemberContributedItemResource(null, $value->id, $value->payment_item_id, $value->name, $value->amount_deposited, $value->balance, $value->status, $value->approve, $value->created_at));
        }
        return $paid_items;
    }

    private function getRegistration($user_id, $year) {
        $paid_items = null;
        $data = $this->getMemberRegistration($user_id, $year)
            ->whereIn('member_registrations.approve', [PaymentStatus::APPROVED, PaymentStatus::PENDING])
            ->select('users.id as user_id', 'member_registrations.*')->first();
        if(!is_null($data)){
            $paid_items = new MemberContributedItemResource(null, $data->user_id, $data->id, 'Registration', $data->amount, 0.0, PaymentStatus::COMPLETE, $data->approve, $data->created_at);
        }

        return $paid_items;
    }


}

