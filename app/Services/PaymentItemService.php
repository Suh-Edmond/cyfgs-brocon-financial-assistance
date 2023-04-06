<?php
namespace App\Services;

use App\Constants\PaymentItemType;
use App\Http\Resources\PaymentItemCollection;
use App\Interfaces\PaymentItemInterface;
use App\Models\PaymentCategory;
use App\Models\PaymentItem;

class PaymentItemService implements PaymentItemInterface {


    public function createPaymentItem($request, $payment_category_id)
    {
        $payment_category = PaymentCategory::findOrFail($payment_category_id);
        PaymentItem::create([
            'name'                => $request->name,
            'amount'              => $request->amount,
            'complusory'          => $request->complusory,
            'payment_category_id' => $payment_category->id,
            'description'         => $request->description,
            'updated_by'          => $request->user()->name,
            'type'                => $request->type,
            'frequency'           => $request->frequency
        ]);
    }

    public function updatePaymentItem($request, $payment_item_id, $payment_category_id)
    {
        $updated =  $this->findPaymentItem($payment_item_id, $payment_category_id);

        $updated->update([
            'name'          => $request->name,
            'amount'        => $request->amount,
            'complusory'    => $request->complusory,
            'description'   => $request->description,
            'type'          => $request->type,
            'frequency'     => $request->frequency
        ]);
    }

    public function getPaymentItemsByCategory($payment_category_id)
    {
        $total = 0.0;
        $payment_items = $this->gePaymentItems($payment_category_id)
                                ->orderBy('payment_items.name', 'ASC')
                                ->get();
        foreach($payment_items as $payment_item){
            $total += $payment_item->amount;
        }

        return new PaymentItemCollection($payment_items, $total);

    }

    public function getPaymentItem($id, $payment_category_id)
    {
        return $this->findPaymentItem($id, $payment_category_id);

    }

    public function deletePaymentItem($id, $payment_category_id)
    {
        $payment_item = $this->findPaymentItem($id, $payment_category_id);

        $payment_item->delete();
    }

    public function filterPaymentItems($category_id, $is_compulsory) {
        $total = 0.0;
        $payment_items = $this->gePaymentItems($category_id)->where('complusory', $is_compulsory)->orderBy('payment_items.name', 'ASC')->get();

        foreach($payment_items as $payment_item){
            $total += $payment_item->amount;
        }

        return new PaymentItemCollection($payment_items, $total);
    }

    public function getPaymentItems() {
        return PaymentItem::all();
    }


    public function getPaymentItemByType()
    {
        return PaymentItem::where('type', PaymentItemType::REGISTRATION)->get();
    }

    private function findPaymentItem($id, $payment_category_id)
    {
        return PaymentItem::select('payment_items.*')->join('payment_categories', ['payment_categories.id' => 'payment_items.payment_category_id'])
            ->where('payment_items.id', $id)
            ->where('payment_items.payment_category_id', $payment_category_id)
            ->firstOrFail();
    }

    private  function gePaymentItems($payment_category_id) {
        return PaymentItem::select('payment_items.*')
            ->join('payment_categories', ['payment_categories.id'  => 'payment_items.payment_category_id'])
            ->where('payment_items.payment_category_id', $payment_category_id);

    }

}
