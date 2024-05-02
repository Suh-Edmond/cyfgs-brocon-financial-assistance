<?php
namespace App\Interfaces;

interface IncomeActivityInterface {

    public function createIncomeActivity($request, $id);

    public function updateIncomeActivity($request, $id);

    public function getIncomeActivities($organisation_id, $request);

    public function getIncomeActivity($id);

    public function deleteIncomeActivity($id);

    public function approveIncomeActivity($id, $type);

    public function filterIncomeActivity($request);

    public function generateIncomeActivityPdf();

}
