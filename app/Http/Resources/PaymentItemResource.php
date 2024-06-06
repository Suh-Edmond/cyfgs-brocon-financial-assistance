<?php

namespace App\Http\Resources;

use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentItemResource extends JsonResource
{
    use ResponseTrait, HelpTrait;
    private $paymentItemQuarters;
    private $paymentItemMonths;
    public function __construct($resource, $paymentItemQuarters = [], $paymentItemMonths = [])
    {
        parent::__construct($resource);
        $this->paymentItemMonths = $paymentItemMonths;
        $this->paymentItemQuarters = $paymentItemQuarters;
    }

    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'amount'                => $this->amount,
            'description'           => $this->description,
            'compulsory'            => $this->compulsory,
            'payment_category_id'   => $this->paymentCategory->id,
            'payment_category_name' => $this->paymentCategory->name,
            'type'                  => $this->type,
            'frequency'             => $this->frequency,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
            'session'               => $this->session,
            'references'            => collect($this->getReferenceResource($this->reference))->sortBy('name')->toArray(),
            'deadline'              => $this->deadline,
            'deadline_state'        => Carbon::now()->lessThan($this->deadline) ? "ACTIVE" : "EXPIRED",
            'payment_item_months'   => $this->paymentItemMonths,
            'payment_item_quarters'  => $this->paymentItemQuarters,
            'is_range'              => $this->is_range,
            'start_amount'          => $this->start_amount,
            'end_amount'            => $this->end_amount
        ];
    }


}
