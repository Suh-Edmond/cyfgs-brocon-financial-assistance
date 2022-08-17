<?php

namespace App\Services;

use App\Interfaces\ExpenditureCategoryInterface;
use App\Models\ExpenditureCategory;
use App\Models\Organisation;

class ExpenditureCategoryService implements ExpenditureCategoryInterface {

    public function createExpenditureCategory($request, $id)
    {
        $organisation = Organisation::findOrFail($id);
        ExpenditureCategory::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'organisation_id'   => $organisation->id
        ]);
    }

    public function updateExpenditureCategory($request, $id, $organisation_id)
    {
        $update_expenditure_category = $this->findExpenditureCategory($id, $organisation_id);
        $update_expenditure_category->update([
            'name'              => $request->name,
            'description'       => $request->description,
        ]);
    }

    public function getExpenditureCategories($organisation_id)
    {
        $expendiure_categories = ExpenditureCategory::where('organisation_id', $organisation_id)->get();

        return $expendiure_categories;
    }

    public function getExpenditureCategory($id, $organisation_id)
    {
        $expendiure_category = $this->findExpenditureCategory($id, $organisation_id);

        return $expendiure_category;
    }

    public function deleteExpenditureCategory($id, $organisation_id)
    {
        $expendiure_category = $this->findExpenditureCategory($id, $organisation_id);

        $expendiure_category->delete();

    }

    private function findExpenditureCategory($id, $organisation_id)
    {
        $update_expenditure_category = ExpenditureCategory::select('expenditure_categories.*')
                                        ->join('organisations', ['organisations.id' => 'expenditure_categories.organisation_id'])
                                        ->where('expenditure_categories.id', $id)
                                        ->where('expenditure_categories.organisation_id', $organisation_id)
                                        ->firstOrFail();
        return $update_expenditure_category;
    }
}
