<?php

use App\Models\Subscription;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/




Broadcast::channel('payments.{subscriptionId}.{paymentId}', function ($user, $subscriptionId, $paymentId) {

    logger()->info('enter execution');
    // 1. Find the subscription
    $subscription = Subscription::where('id', $subscriptionId)->first();

    logger()->info("subscription is " . $subscription);

    if (!$subscription) {
        logger()->error("Broadcast Auth: Subscription not found - ID: $subscriptionId");
        return false;
    }

    // 2. Check if the payment exists for this sub
    $paymentExists = $subscription->payments()
        ->where('transaction_id', $paymentId)
        ->exists();

    if (!$paymentExists) {
        logger()->error("Broadcast Auth: Payment not found for Sub: $subscriptionId");
        return false;
    }

    logger()->info("payment is " . $paymentExists);

    // 3. IMPORTANT: Security Check
    // Verify the authenticated $user actually owns this subscription/payment
    $isOwner = $subscription->organisation->users()
        ->where('users.id', $user->id)
        ->exists();

    logger()->info("Broadcast Auth Result: " . ($isOwner ? 'Authorized' : 'Denied') . " for User: {$user->id}");

    return true;
});
