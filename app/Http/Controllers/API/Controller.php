<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

 /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Akateh School Management API Documentation",
     *      description="This is an API Documentation for the Aketeh School Management System.It contains all endpoints to all resources for the Akateh School Management Software.
     *      Done by the Akateh Dev Team",
     *      @OA\Contact(
     *          email="akateh@akateh.com",
     *          name="Akateh API Support"
     *      ),
     *      @OA\License(
     *          name="Akateh License 1.0",
     *          url="http://www.akateh.com/licenses/LICENSE-1.0.html"
     *      )
     * )
     *
     */
class Controller {

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
