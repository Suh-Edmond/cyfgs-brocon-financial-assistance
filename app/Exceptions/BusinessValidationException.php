<?php

namespace App\Exceptions;

use Exception;

class BusinessValidationException extends Exception
{
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (BusinessValidationException $e) {
            //
        });
    }
}
