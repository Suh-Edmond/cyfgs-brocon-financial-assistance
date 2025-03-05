<?php

namespace App\Services;

use App\Http\Resources\PaymentCategoryCollection;
use App\Interfaces\PaymentCategoryInterface;
use App\Models\Organisation;
use App\Models\PaymentCategory;


class PaymentCategoryService implements PaymentCategoryInterface {

    public function createPaymentCategory($request, $id)
    {
        $organisation = Organisation::findOrFail($id);
        $payment_category = PaymentCategory::find($request->id);
        if(is_null($payment_category)){

            PaymentCategory::create([
                'code'              => $request->code,
                'name'              => $request->name,
                'description'       => $request->description,
                'organisation_id'   => $organisation->id,
                'updated_by'        =>$request->user()->name,
            ]);
        }else{
            $payment_category->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_by'        =>$request->user()->name,
            ]);
        }
    }

    public function updatePaymentCategory($request, $id, $organisation_id)
    {
        $updated = PaymentCategory::findOrFail($id);
        $updated->name          = $request->name;
        $updated->description   = $request->description;
        $updated->save();
    }

    public function getPaymentCategories($request)
    {
        $categories = PaymentCategory::where('organisation_id', $request->organisation_id);
        if(isset($request->year)){
            $categories = $categories->whereYear('created_at', $request->year);
        }
        if(isset($request->filter)){
            $categories = $categories->where('name', 'LIKE', '%'.$request->filter.'%');
        }
        $payment_categories = isset($request->per_page) ? $categories->orderBy($request->sort_by)->paginate($request->per_page): $categories->orderBy($request->sort_by)->get();
        $total = isset($request->per_page) ? $payment_categories->total() : count($payment_categories);
        $last_page = isset($request->per_page) ? $payment_categories->lastPage(): 0;
        $per_page = isset($request->per_page) ? (int)$payment_categories->perPage() : 0;
        $current_page = isset($request->per_page) ? $payment_categories->currentPage() : 0;

        return new PaymentCategoryCollection($payment_categories, $total, $last_page,
           $per_page, $current_page);
    }

    public function getPaymentCategoriesByOrganisationAndYear($organisation_id, $year)
    {
        $categories = PaymentCategory::where('organisation_id', $organisation_id);
        $categories =  $categories->orderBy('name')->get();
        return $categories;
    }
    public function filterPaymentCategory($request){
        return $this->getPaymentCategories($request);
    }
    public function getPaymentCategory($id, $organisation_id)
    {
        return $this->findPaymentCategory($id, $organisation_id);
    }
    public function deletePaymentCategory($id, $organisation_id)
    {
        $payment_category = $this->findPaymentCategory($id, $organisation_id);

        $payment_category->delete();
    }

    private function findPaymentCategory($id, $organisation_id)
    {
        return PaymentCategory::findOrFail($id);
    }
}
