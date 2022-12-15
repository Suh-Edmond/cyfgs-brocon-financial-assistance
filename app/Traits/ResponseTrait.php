<?php

namespace App\Traits;

use App\Constants\Roles;
use App\Http\Resources\ExpenditureDetailResource;
use App\Models\CustomRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ResponseTrait
{
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendError($error, $message, $code = 404)
    {
        $response = [
            'success' => false,
            'error' => $error,
            'message' => $message,
            'code'    => $code
        ];


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



}
