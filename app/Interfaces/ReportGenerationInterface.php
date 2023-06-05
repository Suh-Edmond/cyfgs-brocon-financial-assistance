<?php
namespace App\Interfaces;

interface ReportGenerationInterface {
    public function generateReportPerActivity($id);

    public function generateQuarterlyReport($request);

    public function downloadQuarterlyReport($request);

}
