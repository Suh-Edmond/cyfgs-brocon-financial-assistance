<?php

namespace App\Traits;
use Illuminate\Support\Facades\Http;

trait OpenAIClient
{
    public function getRecommendationFromOpenAI($paymentContributions)
    {

        return Http::withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer '.env('OPEN_AI_API_KEY'),
        ])->post(env('OPEN_AI_API_LINK'), [
            "model" => "gpt-4o-mini",
            "store" => true,
            "messages" => [
                ["role" => "user", "content" => "You are a financial consultant, your clients gives you a list of members contributions for a given activity to make evaluations and recommendations."],

                ["role" => "user", "content" =>  $this->buildRequestData($paymentContributions)],

                ["role" => "user", "content" => "Analyze the data briefly, concisely, and provide a concrete and short steps to improve members contributions for activities with similar payment frequency and expected amount"]
            ]
        ]);
    }

    private function buildRequestData($paymentContributions)
    {
        $formattedString = '';
        foreach ($paymentContributions as $item) {
            foreach ($item as $key => $value) {
                if (is_array($value)) {
                    $formattedString .= "$key: " . json_encode($value) . "\n"; // Encode nested arrays as JSON
                } else {
                    $formattedString .= "$key: $value\n";
                }
            }
        }

        return $formattedString;
    }
}
