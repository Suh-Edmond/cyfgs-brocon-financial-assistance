<?php

namespace App\Http\Controllers;


 use App\Http\Resources\IncomeActivityResource;
use App\Http\Requests\IncomeActivityRequest;
use App\Services\IncomeActivityService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class IncomeActivityController extends Controller
{

    use ResponseTrait, HelpTrait;

    private IncomeActivityService $income_activity_service;

    public function __construct(IncomeActivityService $income_activity_service)
    {
        $this->income_activity_service = $income_activity_service;
    }


    public function createIncomeActivity(IncomeActivityRequest $request, $id)
    {
        $this->income_activity_service->createIncomeActivity($request, $id);

        return $this->sendResponse('success', 'Income activity saved successfully');
    }

    public function updateIncomeActivity(IncomeActivityRequest $request,  $id)
    {
        $this->income_activity_service->updateIncomeActivity($request, $id);

        return $this->sendResponse('success', 'Income activity updated successfully');
    }


    public function getIncomeActivitiesByOrganisation($id, Request $request)
    {
        $income_activities = $this->income_activity_service->getIncomeActivities($id, $request);

        return $this->sendResponse(($income_activities), 200);
    }


    public function getIncomeActivity($id)
    {
        $income_activity = $this->income_activity_service->getIncomeActivity($id);

        return $this->sendResponse(new IncomeActivityResource($income_activity), 200);
    }


    public function deleteIncomeActivity($id)
    {
        $this->income_activity_service->deleteIncomeActivity($id);

        return $this->sendResponse('success', 'Income activity deleted successfully');
    }

    public function approveIncomeActivity($id, Request $request)
    {
        $this->income_activity_service->approveIncomeActivity($id, $request->type);

        return $this->sendResponse('success', 'Income Activity approved successfully');
    }


    public function filterIncomeActivity(Request $request)
    {
        $income_activities = $this->income_activity_service->filterIncomeActivity($request);

        return $this->sendResponse(($income_activities), 200);
    }

    public function prepareData(Request $request) {
        $activities      = $this->filterIncomeActivity($request);

        $activities      = json_decode(json_encode($activities))->original->data;
        return $activities;
    }

    //I will have to pass the parameters in the request and run the query to download the data
    public function generateIncomeActivityPDF(Request $request)
    {
         $organisation      = $request->user()->organisation;

        $income_activities = $this->prepareData($request);
        $total             = $this->income_activity_service->calculateTotal($income_activities->data);

        $admins            = $this->getOrganisationAdministrators();
        $president         = count($admins) == 3 ? $admins[1] : null;
        $treasurer         = count($admins) == 3 ? $admins[2]: null;
        $fin_sec           = count($admins) == 3 ? $admins[0] : null;

        $data = [
            'title'               => !is_null($request->payment_item_name)?'Income Activities for '. $request->payment_item_name:'Income Activities for Year '.$request->year,
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'income_activities'   => $income_activities->data,
            'total'               => $total,
            'president'           => $president,
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'organisation_logo'    => env('FILE_DOWNLOAD_URL_PATH').$organisation->logo
        ];

        $pdf = PDF::loadView('IncomeActivities.IncomeActivities', $data);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('IncomeActivities.pdf');
    }
}
