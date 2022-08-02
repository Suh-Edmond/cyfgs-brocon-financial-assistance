<?php

namespace App\Services;

use App\Interfaces\ExpenditureCategoryInterface;
use App\Models\ExpenditureCategory;
use App\Models\Organisation;
use Illuminate\Support\Facades\DB;

class ExpenditureCategoryService implements ExpenditureCategoryInterface {

    public function createExpenditureCategory($request, $id)
    {
        $organisation = Organisation::findOrFail($id);
        if(! $organisation){
            return response()->json(['message' => 'Oganisation not found'], 404);
        }
        ExpenditureCategory::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'organisation_id'   => $organisation->id
        ]);
    }

    public function updateExpenditureCategory($request, $id, $organisation_id)
    {
        $update_expenditure_category = $this->findExpenditureCategory($id, $organisation_id);
        if(! $update_expenditure_category){
            return response()->json(['message'=> 'Expenditure Category not found', 'status' => '404'], 404);
        }
        $update_expenditure_category->update([
            'name'              => $request->name,
            'description'       => $request->description,
        ]);
    }

    public function getExpenditureCategories($organisation_id)
    {
        $expendiure_categories = ExpenditureCategory::where('organisation_id', $organisation_id);

        return $expendiure_categories->toArray();
    }

    public function getExpenditureCategory($id, $organisation_id)
    {
        $expendiure_category = $this->findExpenditureCategory($id, $organisation_id);
        if(! $expendiure_category){
            return response()->json(['message'=> 'Expenditure Category not found', 'status' => '404'], 404);
        }

        return $expendiure_category;
    }

    public function deleteExpenditureCategory($id, $organisation_id)
    {
        $expendiure_category = $this->findExpenditureCategory($id, $organisation_id);
        if(! $expendiure_category){
            return response()->json(['message'=> 'Expenditure Category not found', 'status' => '404'], 404);
        }

        $expendiure_category->delete();

    }

    private function findExpenditureCategory($id, $organisation_id)
    {
        $update_expenditure_category = DB::table('expenditure_categories')
                                        ->join('organisations', ['organisations.id' => 'expenditure_categories.organisation_id'])
                                        ->where('expenditure_categories.id', $id)
                                        ->where('expenditure_categories.organisation_id', $organisation_id)
                                        ->first();
        return $update_expenditure_category;
    }
}
