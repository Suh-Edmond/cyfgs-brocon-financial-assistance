<?php

namespace App\Services;

use App\Interfaces\UserContributionInterface;
use App\Models\PaymentItem;
use App\Models\User;
use App\Models\UserContribution;
use Ramsey\Uuid\Uuid;
use App\Constants\TransactionStatus;

class UserContributionService implements UserContributionInterface {

    public function createUserContribution($request)
    {
        $user = $this->findUser($request->user_id);
        $payment_item = $this->findPaymentItem($request->payment_item_id);
        $amount_to_be_deposited = $this->getLastContributionByUserPerItem($payment_item->id, $user->id) + $request->amount_deposited;
        UserContribution::create([
            'code'              => Uuid::uuid4(),
            'amount_deposited'  => $amount_to_be_deposited,
            'comment'           => $request->comment,
            'user_id'           => $user->id,
            'payment_item_id'   => $payment_item->id,
            'status'            => $this->checkUserContributionStatus($payment_item, $amount_to_be_deposited),
        ]);
    }

    public function updateUserContribution($request, $id)
    {
        $user_contribution = $this->findUserContributionById($id);
        $user_contribution->update([
            'amount_deposited' => $request->amount_deposited,
            'comment'          => $request->comment,
        ]);
    }

    public function getUserContributionsByItem($payment_item)
    {
        $user_contributions = UserContribution::select('user_contributions.*')
                                                ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                                                ->where('user_contributions.payment_item_id', $payment_item)
                                                ->get()
                                                ->toArray();
        return $user_contributions;
    }

    public function getUserContributionsByUser($user_id)
    {
        $user_contributions = UserContribution::select('user_contributions.*')
                                                ->join('users', ['users.id' => 'user_contributions.user_id'])
                                                ->where('user_contributions.user_id', $user_id)
                                                ->get()
                                                ->toArray();
        return $user_contributions;
    }

    public function getContributionByUserAndItem($payment_item_id, $user_id)
    {
        $user_contributions = $this->getUserContributionsByUser($user_id);
        $user_contributions =   array_filter($user_contributions, function($payment_item_id, $item) {
                                    return $item->payment_item_id == $payment_item_id;
                                });
        return $user_contributions;
    }

    public function deleteUserContribution($id)
    {
        $user_contribution =  $this->findUserContributionById($id);

        $user_contribution->delete();
    }

    public function approveUserContribution($id)
    {
        $user_contribution =  $this->findUserContributionById($id);

        $user_contribution->update(['approve' => true]);
    }


    public function filterContribution($status, $payment_item)
    {
        $user_contributions = UserContribution::select('user_contributions.*')
                                                ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                                                ->where('user_contributions.payment_item_id', $payment_item)
                                                ->where('user_contributions.status', $status)
                                                ->get()
                                                ->toArray();

        return $user_contributions;
    }

    public function getContribution($id)
    {
        return $this->findUserContributionById($id);
    }


    private function findUser($id)
    {
        return User::findOrFail($id);
    }

    private function findPaymentItem($payment_item)
    {
        return PaymentItem::findOrFail($payment_item);
    }

    private function checkUserContributionStatus($payment_item, $amount)
    {
        if($payment_item->amount == $amount){
            $status = TransactionStatus::COMPLETE;
        }else{
            $status = TransactionStatus::INCOMPLETE;
        }

        return $status;
    }

    private function findUserContributionById($id)
    {
        return UserContribution::findOrFail($id);
    }

    private function getLastContributionByUserPerItem($payment_item, $user)
    {
        $amount_deposited = UserContribution::select('user_contributions.*')
                                                ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
                                                ->join('users', ['users.id' => 'user_contributions.user_id'])
                                                ->where('user_contributions.payment_item_id', $payment_item)
                                                ->where('user_contributions.user_id', $user)
                                                ->lastest()->pluck('amount_deposited');

        return $amount_deposited;
    }
}

