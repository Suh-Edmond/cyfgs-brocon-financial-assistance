<?php
namespace App\Services;

use App\Interfaces\PaymentItemInterface;
use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use Illuminate\Support\Facades\DB;

class PaymentItemService implements PaymentItemInterface {


    public function createPaymentItem($request, $paymant_category_id)
    {
        $paymant_category = PaymentCategory::findOrFail($paymant_category_id);
        PaymentItem::create([
            'name'                => $request->name,
            'amount'              => $request->amount,
            'complusory'          => $request->complusory,
            'payment_category_id' => $paymant_category->id,
        ]);
    }

    public function updatePaymentItem($request, $payment_item_id, $paymant_category_id)
    {
        $updated =  $this->findPaymentItem($payment_item_id, $paymant_category_id);
        if(! $updated){
            return response()->json(['message' => 'PaymentItem not found', 'status'=> '404'], 404);
        }
        $updated->update([
            'name'          => $request->name,
            'amount'        => $request->amount,
            'complusory'    => $request->complusory
        ]);
    }

    public function getPaymentItemsByCategory($payment_category_id)
    {
        $payment_items = PaymentItem::where('payment_category_id', $payment_category_id);

        return $payment_items->toArray();
    }

    public function getPaymentItem($id, $paymant_category_id)
    {
        $payment_item = $this->findPaymentItem($id, $paymant_category_id);
        if(! $payment_item){
            return response()->json(['message' => 'PaymentItem not found', 'status'=> '404'], 404);
        }

        return $payment_item;

    }

    public function deletePaymentItem($id, $paymant_category_id)
    {
        $payment_item = $this->findPaymentItem($id, $paymant_category_id);
        if(! $payment_item){
            return response()->json(['message' => 'PaymentItem not found', 'status'=> '404'], 404);
        }

        $payment_item->delete();
    }

    private function findPaymentItem($id, $payment_category_id)
    {
        $updated =  PaymentItem::select('payment_items.*')->join('payment_categories', ['payment_categories.id' => 'payment_items.category_id'])
                    ->where('payment_items.id', $id)
                    ->where('payment_items.payment_category_id', $payment_category_id)
                    ->first();
        return $updated;
    }
}
