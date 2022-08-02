<?php
namespace App\Interfaces;

interface ExpenditureDetailInterface {

    public function createExpenditureDetail($request, $id);

    public function updateExpenditureDetail($request, $id, $expenditure_item_id);

    public function getExpenditureDetails($expenditure_item_id);

    public function getExpenditureDetail($id, $expenditure_item_id);

    public function deleteExpenditureDetail($id, $expenditure_item_id);

    public function approveExpenditureDetail($id);

    public function calculateExpenditureBalance($id, $expenditure_item_id);

    public function filterExpenditureDetail($item, $status);

}
