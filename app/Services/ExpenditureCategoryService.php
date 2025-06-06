<?php

namespace App\Services;

use App\Http\Resources\ExpenditureCategoryCollection;
use App\Interfaces\ExpenditureCategoryInterface;
use App\Models\ExpenditureCategory;
use App\Models\Organisation;

class ExpenditureCategoryService implements ExpenditureCategoryInterface {

    public function createExpenditureCategory($request, $organisation_id)
    {
        $organisation = Organisation::findOrFail($organisation_id);
        $expenditure_category = ExpenditureCategory::find($request->id);
        if(is_null($expenditure_category)){
            ExpenditureCategory::create([
                'code'              => $request->code,
                'name'              => $request->name,
                'description'       => $request->description,
                'organisation_id'   => $organisation->id,
                'updated_by'        => $request->user()->name,
            ]);
        }else {
            $expenditure_category->update([
                'code'        => $request->code,
                'name'        => $request->name,
                'description' => $request->description,
                'updated_by'  => $request->user()->name,
            ]);
        }
    }

    public function updateExpenditureCategory($request, $id, $organisation_id)
    {
        $update_expenditure_category = $this->findExpenditureCategory($id, $organisation_id);
        $update_expenditure_category->update([
            'name'              => $request->name,
            'description'       => $request->description,
        ]);
    }

    public function getExpenditureCategories($organisation_id, $request)
    {
        $categories = ExpenditureCategory::where('organisation_id', $organisation_id);
        if(isset($request->year)){
            $categories = $categories->whereYear('created_at', $request->year);
        }
        if(isset($request->filter)){
            $categories = $categories->where('name', 'LIKE', '%'.$request->filter.'%');
        }
        $expenditure_categories = isset($request->per_page) ? $categories->orderBy($request->sort_by)->paginate($request->per_page): $categories->orderBy($request->sort_by)->get();

        $total = isset($request->per_page) ? $expenditure_categories->total() : count($expenditure_categories);
        $last_page = isset($request->per_page) ? $expenditure_categories->lastPage(): 0;
        $per_page = isset($request->per_page) ? (int)$expenditure_categories->perPage() : 0;
        $current_page = isset($request->per_page) ? $expenditure_categories->currentPage() : 0;

        return new ExpenditureCategoryCollection($expenditure_categories, $total, $last_page,
            $per_page, $current_page);

    }

    public function getExpenditureCategoriesByOrganisationYear($organisation_id, $year)
    {
        $categories = ExpenditureCategory::where('organisation_id', $organisation_id);
        if(isset($year)){
            $categories = $categories->whereYear('created_at', $year);
        }
        return $categories->orderBy('name')->get();
    }


    public function getExpenditureCategory($id, $organisation_id)
    {
        return $this->findExpenditureCategory($id, $organisation_id);
    }

    public function deleteExpenditureCategory($id, $organisation_id)
    {
        $expenditure_category = $this->findExpenditureCategory($id, $organisation_id);

        $expenditure_category->delete();

    }

    private function findExpenditureCategory($id, $organisation_id)
    {
        return ExpenditureCategory::where('organisation_id', $organisation_id)->where('id', $id)->firstOrFail();
    }

    public function filterExpenditureCategory($request)
    {
        return ExpenditureCategory::where('organisation_id', $request->organisation_id)->whereYear('created_at', $request->year)->orderBy('name', 'ASC')->get();
    }

    public  function setDataForDownload($request)
    {
        return $this->getExpenditureCategories($request, $request);
    }

    public function getAllExpenditureCategories($organisation_id)
    {
        return ExpenditureCategory::where('organisation_id', $organisation_id)->orderBy('name')->get();
    }
}
