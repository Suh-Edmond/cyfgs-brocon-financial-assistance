<?php
namespace App\Interfaces;

interface IncomeActivityInterface {

    public function createIncomeActivity($request, $id);

    public function updateIncomeActivity($request, $id);

    public function getIncomeActivities($organisation_id);

    public function getIncomeActivity($id);

    public function deleteIncomeActivity($id);

    public function approveIncomeActivity($id);

    public function filterIncomeActivity($organisation_id, $month, $year, $status);

    public function generateIncomeActivityPdf();

}
