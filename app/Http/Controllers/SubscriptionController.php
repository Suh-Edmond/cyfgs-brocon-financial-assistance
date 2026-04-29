<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionPaymentFeeRequest;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionAmountResource;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ResponseTrait;
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function fetchAllClientSubscriptions(Request $request)
    {
        $subscriptions = $this->subscriptionService->fetchAllClientSubscriptions($request);

        return $this->sendResponse($subscriptions, 'success');
    }

    public function filterClientSubscription(Request $request)
    {
        $subscriptions = $this->subscriptionService->filterSubscription($request);

        return $this->sendResponse($subscriptions, 'success');
    }

    public function createSubscription(SubscriptionRequest $request)
    {
        $data = $this->subscriptionService->createSubscription($request);

        return $this->sendResponse($data, 'Subscription created successfully');
    }

    public function getActivateSubscriptionTrial(SubscriptionRequest $request)
    {
        $data = $this->subscriptionService->getActivateSubscriptionTrial($request);

        return $this->sendResponse($data, 'Subscription trial activated successfully');
    }

    public function showSubscription($id)
    {
        $data = $this->subscriptionService->getSubscription($id);

        return $this->sendResponse($data, 'success');
    }

    public function update(UpdateSubscriptionRequest $request,  $id)
    {
        $data = $this->subscriptionService->updateSubscription($id, $request);

        return $this->sendResponse($data, 'Subscription updated successfully');
    }

    public function destroy(Subscription $subscription)
    {
        $this->subscriptionService->deleteSubscription($subscription);

        return $this->sendResponse('success', 'Subscription successfully been removed');
    }

    public function deleteClientSubscription($id, Request $request)
    {
        $this->subscriptionService->deleteClientSubscription($id, $request);

        return $this->sendResponse('success', 'Subscription successfully been removed');
    }

    public function computeTotalSubscriptionAmount(SubscriptionPaymentFeeRequest $request, $id)
    {
        $data = $this->subscriptionService->computeTotalSubscriptionAmount($id, $request->input('organisation_id'));

        $response = new SubscriptionAmountResource($data, $data['subscription'], max(round($data['totalAmount'], 2), 0), $data['chargeable_fee']);

        return $this->sendResponse($response, 'success');
    }

    public function getClientIncompleteSubscription(Request $request)
    {
        $data = $this->subscriptionService->getClientIncompleteSubscription($request);

        return $this->sendResponse($data, 'successfully');
    }

    public function getSubscriptionByClient(Request $request)
    {
        $subscriptions = $this->subscriptionService->getSubscriptionsByClient($request);
        return $this->sendResponse($subscriptions, 'success');
    }
}
