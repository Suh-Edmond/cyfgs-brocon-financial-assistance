<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendError($error, $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
            'code'    => $code
        ];

        return response($response, $code);
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
}
