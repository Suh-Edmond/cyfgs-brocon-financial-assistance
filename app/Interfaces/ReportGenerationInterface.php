<?php
namespace App\Interfaces;

interface ReportGenerationInterface {


//
//    public function calculateIncomesForEachMonth($month, $organisation_id);
//
//    public function calculateIncomesForThreeMonths($month_range, $organisation_id);
//
//    public function calculateIncomesForSixMonths($month_range, $organisation_id);
//
//    public function calculateIncomesForYear($year, $organisation_id);
//
//    public function getIncomeActivitiesForThreeMonths($month_range, $organisation_id);
//
//    public function getUserContributionsForThreeMonths($month_range, $organisation_id);
//
//    public function getUserSavingsForThreeMonths($month_range, $organisation_id);
//
//    public function getIncomeActivitiesForEachMonth($month, $organisation_id);
//
//    public function getUserContributionsForEachMonth($month, $organisation_id);
//
//    public function getUserSavingsForEachMonth($month, $organisation_id);

       public function generateReportPerActivity($id);
}
