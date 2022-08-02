<?php
namespace App\Interfaces;

interface ExpenditureItemInterface {

    public function createExpenditureItem($request, $expenditure_category_id);

    public function updateExpenditureItem($request, $id, $expenditure_category_id);

    public function getExpenditureItems($expenditure_category_id);

    public function getExpenditureItem($id, $expenditure_category_id);

    public function deleteExpenditureItem($id, $expenditure_category_id);

    public function approveExpenditureItem($id);

    public function getExpenditureItemByStatus($status);
}
