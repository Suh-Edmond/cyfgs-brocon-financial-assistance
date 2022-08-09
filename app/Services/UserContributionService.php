<?php

namespace App\Services;

use App\Interfaces\UserContributionInterface;
use App\Models\PaymentItem;
use App\Models\User;
use App\Models\UserContribution;
use Ramsey\Uuid\Uuid;

class UserContributionService implements UserContributionInterface {

    public function createUserContribution($request)
    {
        $user = $this->findUser($request->user_id);
        $payment_item = $this->findPaymentItem($request->payment_item_id);
        UserContribution::create([
            'code'              => Uuid::uuid4(),
            'amount_deposited'  => $request->amount_deposited,
            'comment'           => $request->comment,
            'user_id'           => $user->id,
            'payment_item_id'   => $payment_item->id,
            // ''
        ]);
    }

    public function updateUserContribution($request, $id)
    {

    }

    public function getUserContributionsByItem($payment_item)
    {

    }

    public function getUserContributionsByUser($user_id)
    {

    }

    public function getUserContribution($payment_item_id, $user_id)
    {

    }

    public function deleteUserContribution($payment_item_id, $user_id)
    {

    }

    public function approveUserContribution($id)
    {

    }


    private function findUser($id)
    {
        return User::findOrFail($id);
    }

    private function findPaymentItem($payment_item)
    {
        return PaymentItem::findOrFail($payment_item);
    }
}

