<?php

namespace App\Services;

use App\Exceptions\BusinessValidationException;
use App\Http\Resources\SubscriptionAmountResource;
use App\Http\Resources\SubscriptionResource;
use App\Interfaces\SubscriptionInterface;
use App\Models\Organisation;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

class SubscriptionService implements SubscriptionInterface
{

    public function createSubscription($request)
    {
        $subscription_plan = SubscriptionPlan::where('id', $request->subscription_plan_id)->where('status', true)->first();
        $organisation = Organisation::find($request->organisation_id);

        if (is_null($subscription_plan)) {
            throw new \App\Exceptions\BusinessValidationException('Invalid subscription plan selected', 400);
        }
        if (!$organisation) {
            throw new BusinessValidationException("Organisation not found", 404);
        }

        $active_sub = $organisation->subscriptions()->where('status', 'active')->first();

        if ($active_sub) {
            throw new BusinessValidationException("The Organisation currently have an active subscription", 400);
        }

        $subscription = Subscription::updateOrCreate(
            [
                'organisation_id' => $organisation->id,
                'subscription_plan_id' => $subscription_plan->id,
                'status' => 'incomplete'
            ],
            [
                'organisation_id' => $organisation->id,
                'subscription_plan_id' => $subscription_plan->id,
                'auto_renewal' => $request->auto_renewal,
                'is_trail' => $request->auto_renewal ? false : $request->is_trail,
                'current_period_start_date' => null,
                'current_period_end_date' => null,
                'trial_period_start_date' => $request->is_trail ? $this->getTrailDuration()['trial_period_start_date'] : null,
                'trial_period_end_date' => $request->is_trail ? $this->getTrailDuration()['trial_period_end_date'] : null,
                'status' => $request->is_trail ? 'trialing' : 'incomplete',

                'referral_code_discount' => $request->referral_code ? $this->calculateReferralDiscount($subscription_plan->price, $request->referral_code, $organisation) : 0,
            ]
        );

        return new SubscriptionResource($subscription, $request['loginId']);
    }

    public function getActivateSubscriptionTrial($request)
    {
        $subscription_plan = SubscriptionPlan::where('id', $request->subscription_plan_id)->where('status', true)->first();
        $organisation = Organisation::find($request->organisation_id);

        if (is_null($subscription_plan)) {
            throw new \App\Exceptions\BusinessValidationException('Invalid subscription plan selected', 400);
        }
        if (!$organisation) {
            throw new BusinessValidationException("Organisation not found", 404);
        }

        $subscription = Subscription::updateOrCreate(
             [
                'organisation_id' => $organisation->id,
                'subscription_plan_id' => $subscription_plan->id,
                'status' => 'incomplete'
            ],
            [
                'organisation_id' => $organisation->id,
                'subscription_plan_id' => $subscription_plan->id,
                'auto_renewal' => false,
                'is_trail' => true,
                'current_period_start_date' => null,
                'current_period_end_date' => null,
                'trial_period_start_date' => $this->getTrailDuration()['trial_period_start_date'],
                'trial_period_end_date' => $this->getTrailDuration()['trial_period_end_date'],
                'status' => 'trialing',
                'referral_code_discount' =>  0,
            ]
        );

        return new SubscriptionResource($subscription, $request['login_id']);
    }

    public function updateSubscription($id, $request)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription_plan = SubscriptionPlan::where('id', $request->subscription_plan_id)->where('status', true)->first();

        if (is_null($subscription_plan)) {
            throw new \App\Exceptions\BusinessValidationException('Invalid subscription plan selected', 400);
        }
        $subscription->update([
            'subscription_plan_id' => $request->subscription_plan_id,
            'auto_renewal' => $request->auto_renewal,
            'current_period_start_date' => null,
            'current_period_end_date' => null,
            'trial_period_start_date' => $request->is_trail ? $this->getTrailDuration()['trial_period_start_date'] : null,
            'trial_period_end_date' => $request->is_trail ? $this->getTrailDuration()['trial_period_end_date'] : null,
            'status' => $request->is_trail ? 'trialing' : 'incomplete',
            'is_trail' => $request->is_trail
        ]);

        return new SubscriptionResource($subscription);
    }
    public function getSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        return new SubscriptionResource($subscription);
    }
    public function deleteClientSubscription($id, $request)
    {
        return $request->user()->organisation()->subscriptions()->where('id', $id)->delete();
    }

    public function deleteSubscription($subscription)
    {
        return $subscription->delete();
    }

    public function filterSubscription($request)
    {
        $subscriptions = Subscription::query()
            ->when($request->organisation_id, function ($query) use ($request) {
                $query->where('organisation_id', $request->organisation_id);
            })
            ->when($request->subscription_plan_id, function ($query) use ($request) {
                $query->where('subscription_plan_id', $request->subscription_plan_id);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->is_trail, function ($query) use ($request) {
                $query->where('is_trail', $request->is_trail);
            })
            ->orderBy('created_at', 'desc');

        $subscription_items_response =   isset($request->per_page) ? $subscriptions->paginate($request->per_page) : $subscriptions->paginate(10);

        $total         =   isset($request->per_page) ? $subscription_items_response->total()         : count($subscription_items_response);
        $last_page     =   isset($request->per_page) ? $subscription_items_response->lastPage()      : 0;
        $per_page      =   isset($request->per_page) ? (int) $subscription_items_response->perPage() : 0;
        $current_page  =   isset($request->per_page) ? $subscription_items_response->currentPage() : 0;

        return new SubscriptionResource($subscription_items_response, $total, $last_page, $per_page, $current_page);
    }


    public function fetchAllClientSubscriptions($request)
    {
        $subscriptions = $request->user()->organisation()->subscriptions()->orderBy('created_at', 'desc')->get();
        return SubscriptionResource::collection($subscriptions);
    }

    public function computeTotalSubscriptionAmount($subscriptionId, $orgId)
    {
        $subscription = Subscription::where('id', $subscriptionId)->where('organisation_id', $orgId)->firstOrFail();
        $totalAmount = $subscription->subscriptionPlan->price;

        if ($subscription->status === 'cancelled') {
            return new SubscriptionAmountResource($subscription, $subscription->id, 0, (int) config('app.payment_charge_fee_percentage'));
        }

        if ($subscription->subscriptionPlan->is_discount_applicable && $subscription->subscriptionPlan->discount_percentage > 0) {
            $totalAmount = (float) $subscription->subscriptionPlan->price - ((float)$subscription->subscriptionPlan->price * ((float)$subscription->subscriptionPlan->discount_percentage / 100));
        }

        if ((float) $subscription->referral_code_discount > 0) {
            $totalAmount = $totalAmount > 0 ? $totalAmount - (float) $subscription->referral_code_discount : (float) $subscription->subscriptionPlan->price - (float) $subscription->referral_code_discount;
        }

        $totalAmount = $totalAmount * $subscription->billing_duration;

        return ["subscription" => $subscription, "totalAmount" => max(round($totalAmount, 2), 0), "chargeable_fee" => (int) config('app.payment_charge_fee_percentage')];
    }

    public function getClientIncompleteSubscription($request)
    {
        $user = User::find('id', $request['loginId'])->first();
        if (!$user) {
            throw new BusinessValidationException("Invalid account or user", 403);
        }
        $subscriptions = $user->organisation->subscriptions('status', 'incomplete')->get();

        return SubscriptionResource::collection($subscriptions);
    }

    public function getSubscriptionsByClient($request)
    {
        $subscriptions = Subscription::where('organisation_id', $request->organisation_id)->orderBy('created_at', 'desc')->get();
        return SubscriptionResource::collection($subscriptions);
    }

    private function getTrailDuration()
    {
        $start = \Carbon\Carbon::now();
        $end = \Carbon\Carbon::now()->addMonths(1);
        return [
            'trial_period_start_date' => $start,
            'trial_period_end_date' => $end
        ];
    }

    private function calculateReferralDiscount($subscription_plan_price, $referral_code, $organisation)
    {
        $referredOrganisation = Organisation::where('referral_code', $referral_code)->first();
        $discountAmount = 0;
        if ($referredOrganisation) {
            $discountPercentage = config('app.subscription_referral_bonus_percent', 10);
            $discountAmount = ($subscription_plan_price * $discountPercentage) / 100;

            $organisation->update([
                'referred_code' => $referral_code
            ]);

            $this->updateReferralCount($referredOrganisation);
        }
        return $discountAmount;
    }

    private function updateReferralCount($referredOrganisation)
    {
        if ($referredOrganisation) {
            $referredOrganisation->increment('referral_count');

            $referredOrganisation->save();
        }
    }
}
