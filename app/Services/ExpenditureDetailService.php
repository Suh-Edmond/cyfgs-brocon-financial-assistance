<?php
namespace App\Services;

use App\Interfaces\ExpenditureDetailInterface;
use App\Models\ExpenditureItem;

class ExpenditureDetailService implements ExpenditureDetailInterface {

    public function createExpenditureDetail($request, $id)
    {
        $item = ExpenditureItem::findOrFail($id);

        

    }

    public function updateExpenditureDetail($request, $id, $expenditure_item_id)
    {

    }

    public function getExpenditureDetails($expenditure_item_id)
    {

    }

    public function getExpenditureDetail($id, $expenditure_item_id)
    {

    }

    public function deleteExpenditureDetail($id, $expenditure_item_id)
    {

    }

    public function approveExpenditureDetail($id)
    {

    }

    public function calculateExpenditureBalance($id, $expenditure_item_id)
    {

    }

    public function filterExpenditureDetail($item, $status)
    {

    }
}
