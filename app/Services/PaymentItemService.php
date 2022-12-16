<?php
namespace App\Services;

use App\Http\Resources\PaymentItemCollection;
use App\Http\Resources\PaymentItemResource;
use App\Interfaces\PaymentItemInterface;
use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use Illuminate\Support\Facades\DB;

class PaymentItemService implements PaymentItemInterface {


    public function createPaymentItem($request, $paymant_category_id)
    {
        $payment_category = PaymentCategory::findOrFail($paymant_category_id);
        PaymentItem::create([
            'name'                => $request->name,
            'amount'              => $request->amount,
            'complusory'          => $request->complusory,
            'payment_category_id' => $payment_category->id,
            'description'         => $request->description
        ]);
    }

    public function updatePaymentItem($request, $payment_item_id, $paymant_category_id)
    {
        $updated =  $this->findPaymentItem($payment_item_id, $paymant_category_id);

        $updated->update([
            'name'          => $request->name,
            'amount'        => $request->amount,
            'complusory'    => $request->complusory,
            'description'   => $request->description
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

    public function getPaymentItem($id, $paymant_category_id)
    {
        return $this->findPaymentItem($id, $paymant_category_id);

    }

    public function deletePaymentItem($id, $paymant_category_id)
    {
        $payment_item = $this->findPaymentItem($id, $paymant_category_id);

        $payment_item->delete();
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

    public function filterPaymentItems($category_id, $is_compulsory) {
        $total = 0.0;
        $payment_items = $this->gePaymentItems($category_id)->where('complusory', $is_compulsory)->orderBy('payment_items.name', 'ASC')->get();

        foreach($payment_items as $payment_item){
            $total += $payment_item->amount;
        }

        return new PaymentItemCollection($payment_items, $total);
    }
}
