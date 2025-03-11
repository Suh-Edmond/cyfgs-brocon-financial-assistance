<?php
namespace App\Interfaces;

use App\Http\Requests\GenerateQuarterlyRequest;

interface ReportGenerationInterface {
    public function generateReportPerActivity($id);

    public function generateQuarterlyReport(GenerateQuarterlyRequest $request);

    public function downloadQuarterlyReport(GenerateQuarterlyRequest $request);

    public function generateYearlyReport($request);

    public function downloadYearlyReport($request);
}
