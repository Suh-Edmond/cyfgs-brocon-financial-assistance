<?php

namespace App\Services;

use App\Interfaces\RecommendationInterface;
use App\Traits\OpenAIClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

class RecommendationService implements RecommendationInterface
{
    use OpenAIClient;

    private UserContributionService $userContributionService;

    public function __construct(UserContributionService $userContributionService)
    {
        $this->userContributionService = $userContributionService;
    }

    public function makeRecommendationAboutUserContributions($request)
    {
        $data = $this->buildRequestPayload($request);

        $response = $this->getRecommendationFromOpenAI($data);

        return $this->formatRecommendationResponse(json_decode($response->body(), true)['choices'][0]['message']['content']);

    }

    private function formatRecommendationResponse($msg)
    {
        $msg = str_replace("###", "######", $msg);
        $msg = str_replace("$", "XAF", $msg);
        $formattedRecommendation = "";
        try {
            $formattedRecommendation =  (new CommonMarkConverter())->convert($msg)->getContent();
        } catch (CommonMarkException $e) {
            Log::info($e->getMessage());
        }

        return $formattedRecommendation;
    }

    /**
     * @param Request $request
     *
     * payment_item_id
     * year(session_id)
     */
    private function buildRequestPayload(Request $request)
    {
        $contributions =  $this->userContributionService->getActivityContributions($request);
        $decoded_data = json_decode(json_encode($contributions), true);

        return [
            [
                "Activity" => $decoded_data['payment_item']['name']
            ],
            [
                "frequency of payment" => $decoded_data['payment_item']['frequency']
            ],
            [
                "type of payment activity" => $decoded_data['payment_item']['type']
            ],
            [
                "total member size"    => $decoded_data['member_size']
            ],
            [
                "total expected contribution" => $decoded_data['total_amount_payable']
            ],
            [
                "total amount contributed" => $decoded_data['total_amount']
            ],
            [
                "balance to be contributed" => $decoded_data['total_balance']
            ],
            [
                "percentage of contribution" => $decoded_data['percentage']
            ],
            [
                "list of members individual contributions" => $this->getMembersIndividualContributions($decoded_data['data'])
            ]
        ];
    }

    private function getMembersIndividualContributions($members_contributions)
    {
        $membersContributions = array();

        foreach ($members_contributions as $contribution){

            $membersContributions[] = ["name" => $contribution['user_name'], "total amount contributed" => $contribution['total_amount_deposited'], "total balance" => $contribution['balance']];
        }

        return $membersContributions;

    }
}
