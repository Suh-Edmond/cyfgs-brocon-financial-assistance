<?php

namespace App\Services;

use App\Interfaces\PaymentCategoryInterface;
use App\Models\Organisation;
use App\Models\PaymentCategory;
use Illuminate\Support\Facades\DB;


class PaymentCategoryService implements PaymentCategoryInterface {

    public function createPaymentCategory($request, $id)
    {
        $organisation = Organisation::findOrFail($id);
        PaymentCategory::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'organisation_id'   => $organisation->id
        ]);
    }

    public function updatePaymentCategory($request, $id, $organisation_id)
    {
        $updated = DB::table('payment_categories')
                            ->join('organisations', ['payment_categories.organisation_id' => 'organisations.id'])
                            ->where('organisations.id', $organisation_id)
                            ->where('payment_categories.id', $id)
                            ->firstOrFail();
        $updated->update([
            'name'          => $request->name,
            'description'   => $request->description
        ]);
    }

    public function getPaymentCategories($organisation_id)
    {
        $payment_categories = PaymentCategory::where('organisation_id', $organisation_id)->get();

        return $payment_categories;
    }

    public function getPaymentCategory($id, $organisation_id)
    {

        $payment_category = $this->findPaymentCategory($id, $organisation_id);

        return $payment_category;
    }

    public function deletePaymentCategory($id, $organisation_id)
    {
        $payment_category = $this->findPaymentCategory($id, $organisation_id);

        $payment_category->delete();
    }


    private function findPaymentCategory($id, $organisation_id)
    {
        $payment_category = PaymentCategory::select('payment_categories.*')
                            ->join('organisations', ['payment_categories.organisation_id' => 'organisations.id'])
                            ->where('organisations.id', $organisation_id)
                            ->where('payment_categories.id', $id)
                            ->firstOrFail();

        return $payment_category;
    }
}
