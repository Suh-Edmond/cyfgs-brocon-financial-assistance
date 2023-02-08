<?php

namespace App\Services;

use App\Exceptions\BusinessValidationException;
use App\Interfaces\UserContributionInterface;
use App\Models\PaymentItem;
use App\Models\User;
use App\Models\UserContribution;
use App\Traits\HelpTrait;
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

        if(!$hasCompleted){
            UserContribution::create([
                'code'              => $this->generateCode(10),
                'amount_deposited'  => $request->amount_deposited,
                'comment'           => $request->comment,
                'user_id'           => $user->id,
                'payment_item_id'   => $payment_item->id,
                'status'            => $status,
                'scan_picture'      => $request->scan_picture,
                'updated_by'        => $request->user()->name
            ]);
        }

    }

    public function updateUserContribution($request, $id)
    {
        $user_contribution = $this->findUserContributionById($id);

        $total_amount_contributed = $this->getTotalAmountPaidByUserForTheItem($user_contribution->user_id, $user_contribution->payment_item_id);

        $hasCompleted = $this->verifyExcessUserContribution($total_amount_contributed, $user_contribution->paymentItem->amount);

        if(!$hasCompleted){
            $user_contribution->update([
                'amount_deposited' => $request->amount_deposited,
                'comment'          => $request->comment,
                'scan_picture'     => $request->scan_picture
            ]);
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
        return DB::table('user_contributions')
                                    ->join('users', 'users.id', '=', 'user_contributions.user_id')
                                    ->join('payment_items', 'payment_items.id', '='  ,'user_contributions.payment_item_id')
                                    ->selectRaw('SUM(user_contributions.amount_deposited) as total_amount_deposited, user_contributions.*')
                                    ->where('user_contributions.user_id', $user_id)
                                    ->where('user_contributions.payment_item_id', $payment_item_id)
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
        return UserContribution::join('users', ['users.id' => 'user_contributions.user_id'])
                                ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                                ->where('users.id', $user_id)
                                ->where('payment_items.id', $payment_item_id)
                                ->sum('user_contributions.amount_deposited');
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

    private function getLastContributionByUserPerItem($payment_item, $user)
    {
        return UserContribution::select('user_contributions.*')
                                                ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                                                ->join('users', ['users.id' => 'user_contributions.user_id'])
                                                ->where('user_contributions.payment_item_id', $payment_item)
                                                ->where('user_contributions.user_id', $user)
                                                ->latest()->get();
    }

    private function getTotalAmountContributed($amounts_contributed, $amount_to_be_deposited)
    {
        if(count($amounts_contributed) != 0){
            $total_amount_contributed = $amounts_contributed[0]->amount_deposited + $amount_to_be_deposited;
        }else{
            $total_amount_contributed = $amount_to_be_deposited;
        }

        return $total_amount_contributed;
    }


    private function verifyExcessUserContribution($total_amount_contributed, $payment_item_amount)
    {
        return $total_amount_contributed == $payment_item_amount;
    }


    private function filterPaymentItem($item, $value)
    {
        return $item->payment_item_id === $value;
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
}

