<?php
namespace App\Interfaces;


interface ExpenditureCategoryInterface{

    public function createExpenditureCategory($request, $id);

    public function updateExpenditureCategory($request, $id, $organisation_id);

    public function getExpenditureCategories($organisation_id, $request);

    public function getAllExpenditureCategories($organisation_id);

    public function getExpenditureCategory($id, $organisation_id);

    public function deleteExpenditureCategory($id, $organisation_id);
}
