<?php

namespace App\Http\Controllers;

use App\Services\ReportGenerationService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class GenerateReportController extends Controller
{
    use ResponseTrait, HelpTrait;

    private ReportGenerationService $report_generation_service;

    public function __construct(ReportGenerationService  $generationService)
    {
        $this->report_generation_service = $generationService;
    }

    public function generateReportByActivity($id)
    {

    }
}
