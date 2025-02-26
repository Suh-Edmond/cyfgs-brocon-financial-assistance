<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    use ResponseTrait;
    private RecommendationService $recommendationService;
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function makeRecommendationOnContributions(Request $request){

        return $this->recommendationService->makeRecommendationAboutUserContributions($request);
    }
}
