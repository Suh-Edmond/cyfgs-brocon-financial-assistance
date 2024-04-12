<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUpdateActivitySupportRequest;
use App\Http\Resources\ActivitySupportResource;
use App\Models\User;
use App\Services\ActivitySupportService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ActivitySupportController extends Controller
{
    use ResponseTrait, HelpTrait;

    private ActivitySupportService  $activity_support_service;

    public function __construct(ActivitySupportService  $activity_support_service)
    {
        $this->activity_support_service = $activity_support_service;
    }

    public function fetchAll(Request $request)
    {
        $data = $this->activity_support_service->fetchAllActivitySupport($request);
        return $this->sendResponse(($data), 200);
    }

    public function createActivitySupport(CreateUpdateActivitySupportRequest $request)
    {
        $this->activity_support_service->createActivitySupport($request);

        return $this->sendResponse('success', 201);
    }


    public function fetchActivitySupport($id)
    {
        $data = $this->activity_support_service->getActivitySupport($id);

        return $this->sendResponse(new ActivitySupportResource($data), 200);
    }


    public function updateActivitySupport(CreateUpdateActivitySupportRequest $request,  $id)
    {
        $this->activity_support_service->updateActivitySupport($id, $request);

        return $this->sendResponse('success', 204);

    }


    public function deleteActivitySupport($id)
    {
        $this->activity_support_service->deleteActivitySupport($id);

        return $this->sendResponse('success', 204);
    }

    public function filterActivitySupport(Request $request)
    {
        $data = $this->activity_support_service->filterActivitySupport($request);

        return $this->sendResponse(ActivitySupportResource::collection($data), 200);
    }

    public function getActivitySupportsByPaymentItem($id)
    {
        $data = $this->activity_support_service->getActivitySupportsByPaymentItem($id);

        return $this->sendResponse(ActivitySupportResource::collection($data), 200);
    }

    public function changeActivityState($id, Request $request)
    {
        $this->activity_support_service->changeActivityState($id, $request);

        return $this->sendResponse('success', 204);
    }

    private function prepareData(Request $request) {
        $sponsorships      = $this->fetchAll($request);
        $sponsorships      = json_decode(json_encode($sponsorships))->original->data;
        return $sponsorships;
    }

    public function downloadActivitySupport(Request $request)
    {

        $organisation      = $request->user()->organisation;

        $supports          = $this->prepareData($request);

        $admins            = $this->getOrganisationAdministrators();

        $president         = count($admins) >= 3 ? $admins[1] : null;
        $treasurer         = count($admins) >= 3 ? $admins[2]: null;
        $fin_sec           = count($admins) >= 3 ? $admins[0] : null;
        $data = [
            'title'               => $this->setTitle($request),
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'supports'            => $supports->data,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'total'                => $supports->total_amount,
            'organisation_logo'    => env('FILE_DOWNLOAD_URL_PATH').$organisation->logo
        ];
        $pdf = PDF::loadView('ActivitySupport.Support', $data);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('ActivitySupport.pdf');
    }

    public function setTitle(Request $request){
       if(isset($request->payment_item_label)){
           $title = 'Sponsorships for '.$request->payment_item_label;
       }else {
           $title = 'Organisation Sponsorships';
       }

       return $title;
    }

}
