<?php
namespace App\Traits;
use App\Http\Resources\ExpenditureDetailResource;

trait ResponseTrait {
     /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }


    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    public static function convertBooleanValue($value)
    {
        if($value == 0){
            $response = false;
        }else{
            $response = true;
        }

        return $response;
    }


    public static function generateResponseForExpenditureDetails($details)
    {
        $response = array();

        foreach($details as $detail){
            $balance = ResponseTrait::calculateExpenditureBalance($detail);

            array_push($response, new ExpenditureDetailResource($detail, $balance));
        }

        return $response;
    }


    public static function calculateExpenditureBalance($expenditure_detail)
    {
        $balance = $expenditure_detail->amount_given - $expenditure_detail->amount_spent;

        return $balance;
    }

    private function calculateExpenditureBalanceByExpenditureItem($expenditure_details, $total_item_amount)
    {
        $balance = $total_item_amount - $this->calculateTotalAmountGiven($expenditure_details);
        $balance += $this->calculateTotalAmountGiven($expenditure_details) - $this->calculateTotalAmountSpent($expenditure_details);

        return $balance;

    }

    private function calculateTotalAmountGiven($expenditure_details)
    {
        $total = 0;
        foreach($expenditure_details as $expenditure_detail){
            $total += $expenditure_detail->amount_given;
        }

        return $total;
    }

    private function calculateTotalAmountSpent($expenditure_details)
    {
        $total = 0;
        foreach($expenditure_details as $expenditure_detail){
            $total += $expenditure_detail->amount_spent;
        }

        return $total;
    }
}
