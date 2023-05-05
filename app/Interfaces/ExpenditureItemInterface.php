<?php
namespace App\Interfaces;

interface ExpenditureItemInterface {

    public function createExpenditureItem($request, $expenditure_category_id);

    public function updateExpenditureItem($request, $id, $expenditure_category_id);

    public function getExpenditureItems($expenditure_category_id, $status);

    public function getExpenditureItem($id, $expenditure_category_id);

    public function deleteExpenditureItem($id, $expenditure_category_id);

    public function approveExpenditureItem($id, $type);

    public function getItem($id);

    public function getExpenditureByCategory($expenditure_category_id);

    public function getExpenditureItemsByPaymentItem($item, $request);

    public function downloadExpenditureItems($request);
}
