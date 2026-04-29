<?php

namespace App\Interfaces;

interface SubscriptionInterface
{
    public function createSubscription($request);
    public function updateSubscription($id, $request);
    public function getSubscription($id);
    public function deleteSubscription($id);
    public function filterSubscription($request);
    public function fetchAllClientSubscriptions($request);
    public function deleteClientSubscription($id, $request);
    public function computeTotalSubscriptionAmount($subscriptionId, $request);
    public function getClientIncompleteSubscription($request);
    public function getActivateSubscriptionTrial($request);
    public function getSubscriptionsByClient($request);
}
