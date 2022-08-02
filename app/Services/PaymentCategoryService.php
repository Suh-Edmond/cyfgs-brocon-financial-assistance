<?php

namespace App\Services;

use App\Interfaces\PaymentCategoryInterface;
use App\Models\Organisation;
use App\Models\PaymentCategory;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class PaymentCategoryService implements PaymentCategoryInterface {

    public function createPaymentCategory($request, $id)
    {
        $organisation_id = Organisation::findOrFail($id);
        PaymentCategory::created([
            'name'              => $request->name,
            'description'       =>$request->description,
            'organisation_id'   => $id
        ]);
    }

    public function updatePaymentCategory($request, $id, $organisation_id)
    {
        $updated = DB::table('payment_categories')
                            ->join('organisations', ['payment_categories.organisation_id' => 'organisations.id'])
                            ->where('organisations.id', $organisation_id)
                            ->where('payment_categories.id', $id)
                            ->first();
        if(! $updated){
            return response()->json(['message' => 'PaymentCategory not found', 'status'=> '404'], 404);
        }
        $updated->update([
            'name'          => $request->name,
            'description'   => $request->description
        ]);
    }

    public function getPaymentCategories($organisation_id)
    {
        $payment_categories = PaymentCategory::where('organisation_id', $organisation_id)->get();

        return $payment_categories->toArray();
    }

    public function getPaymentCategory($id, $organisation_id)
    {

        $payment_category = $this->findPaymentCategory($id, $organisation_id);
        if(!$payment_category){
            return response()->json(['message' => 'PaymentCategory not found', 'status'=> '404'], 404);
        }

        return $payment_category;
    }

    public function deletePaymentCategory($id, $organisation_id)
    {
        $payment_category = $this->findPaymentCategory($id, $organisation_id);

        if(!$payment_category){
           return response()->json(['message' => 'PaymentCategory not found', 'status'=> '404'], 404);
        }

        $payment_category->delete();
    }


    private function findPaymentCategory($id, $organisation_id)
    {
        $payment_category = DB::table('payment_categories')
                            ->join('organisations', ['payment_categories.organisation_id' => 'organisations.id'])
                            ->where('organisations.id', $organisation_id)
                            ->where('payment_categories.id', $id)
                            ->first();

        return $payment_category;
    }
}
