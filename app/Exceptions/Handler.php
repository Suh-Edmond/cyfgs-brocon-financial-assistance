<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof ModelNotFoundException && $request->wantsJson()){
            return response()->json(['message' => "Resource not found", "status"=> "404"], 404);
        }
        if ($exception instanceof  BusinessValidationException) {
            return response()->json(['message' => $exception->getMessage(), "status"=> $exception->getCode()], $exception->getCode());
        }
        if ($exception instanceof ResourceNotFoundException){
            return response()->json(['message' => $exception->getMessage(), 'status' => $exception->getCode()], $exception->getCode());
        }
        if ($exception instanceof  EmailException){
            return  response()->json(['message' => $exception->getMessage(), 'status'=>$exception->getCode()], $exception->getCode());
        }
        if ($exception instanceof  UnAuthorizedException){
            return  response()->json(['message' => $exception->getMessage(), 'status'=>$exception->getCode()], $exception->getCode());
        }

        return parent::render($request, $exception);
    }
}
