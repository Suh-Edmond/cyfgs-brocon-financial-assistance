<?php
namespace App\Interfaces;

interface ReportGenerationInterface {
    public function generateReportPerActivity($id);

    public function generateQuarterlyReport($request);

    public function downloadQuarterlyReport($request);

    public function generateYearlyReport($request);

    public function downloadYearlyReport($request);
}
