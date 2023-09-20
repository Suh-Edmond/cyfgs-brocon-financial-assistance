<?php

namespace App\Services;

use App\Http\Resources\PaymentCategoryCollection;
use App\Interfaces\PaymentCategoryInterface;
use App\Models\Organisation;
use App\Models\PaymentCategory;
use Illuminate\Support\Facades\DB;


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
                'code'  => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'updated_by'        =>$request->user()->name,
            ]);
        }
    }

    public function updatePaymentCategory($request, $id, $organisation_id)
    {
        $updated = DB::table('payment_categories')
                            ->join('organisations', 'payment_categories.organisation_id' ,'=', 'organisations.id')
                            ->where('organisations.id', $organisation_id)
                            ->where('payment_categories.id', $id)
                            ->first();

        if(!is_null($updated)){
            $updated->name          = $request->name;
            $updated->description   = $request->description;
            $updated->save();
        }
    }

    public function getPaymentCategories($request)
    {
        $categories = PaymentCategory::where('organisation_id', $request->organisation_id);
        if(!is_null($request->year)){
            $categories = $categories->whereYear('created_at', $request->year);
        }
        $paginated_data =  $categories->orderBy($request->sort_by)->paginate($request->per_page);

        return new PaymentCategoryCollection($paginated_data, $paginated_data->total(), $paginated_data->lastPage(),
            (int)$paginated_data->perPage(), $paginated_data->currentPage());
    }

    public function getPaymentCategoriesByOrganisationAndYear($organisation_id, $year)
    {
        $categories = PaymentCategory::where('organisation_id', $organisation_id);
        if(!is_null($year)){
            $categories = $categories->whereYear('created_at', $year);
        }
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
        return PaymentCategory::select('payment_categories.*')
                            ->join('organisations', ['payment_categories.organisation_id' => 'organisations.id'])
                            ->where('organisations.id', $organisation_id)
                            ->where('payment_categories.id', $id)
                            ->firstOrFail();
    }
}
