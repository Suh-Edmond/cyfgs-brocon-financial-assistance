<?php
namespace App\Interfaces;

interface ExpenditureDetailInterface {

    public function createExpenditureDetail($request, $id);

    public function updateExpenditureDetail($request, $id);

    public function getExpenditureDetails($expenditure_item_id);

    public function getExpenditureDetail($id);

    public function deleteExpenditureDetail($id);

    public function approveExpenditureDetail($id, $type);

    public function filterExpenditureDetail($item, $status);

    public function computeTotalExpendituresByYearly($request);
}
