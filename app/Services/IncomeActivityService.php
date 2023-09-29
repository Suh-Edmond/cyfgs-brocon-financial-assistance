<?php
namespace App\Services;

use App\Constants\PaymentStatus;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\IncomeActivityCollection;
use App\Interfaces\IncomeActivityInterface;
use App\Models\IncomeActivity;
use App\Models\Organisation;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class IncomeActivityService implements IncomeActivityInterface {

    use HelpTrait;
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function createIncomeActivity($request, $id)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $organisation = Organisation::findOrFail($id);
        $payment_item_id = $this->getPaymentItemId($request->payment_item_id);

        IncomeActivity::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'date'              => $request->date,
            'amount'            => $request->amount,
            'venue'             => $request->venue,
            'organisation_id'   => $organisation->id,
            'payment_item_id'   => $payment_item_id,
            'updated_by'        =>$request->user()->name,
            'scan_picture'      => $request->scan_picture,
            'session_id'        => $current_session->id
        ]);
    }

    public function updateIncomeActivity($request, $id)
    {
        $activity = $this->findIncomeActivity($id);

        if($activity->approve == PaymentStatus::PENDING){
            $activity->update([
                'name'              => $request->name,
                'description'       => $request->description,
                'date'              => $request->date,
                'amount'            => $request->amount,
                'venue'             => $request->venue,
            ]);
        }else {
            throw new BusinessValidationException("Income activity cannot be updated after been approved or declined");
        }
    }

    public function getIncomeActivities($organisation_id, $request)
    {
        $incomes =  $this->findIncomeActivities($organisation_id)
                            ->orderBy('income_activities.name', 'ASC');
        $total_income = $this->computeTotalIncomeActivities($incomes->get());
        $paginated_data = $incomes->paginate($request->per_page);

        return new IncomeActivityCollection($paginated_data, $total_income, $paginated_data->total(),
            $paginated_data->lastPage(), (int)$paginated_data->perPage(), $paginated_data->currentPage());
    }

    public function getIncomeActivity($id)
    {
        return $this->findIncomeActivity($id);
    }

    public function deleteIncomeActivity($id)
    {
        $activity = $this->findIncomeActivity($id);

        $activity->delete();
    }

    public function approveIncomeActivity($id, $type)
    {
        $activity = $this->findIncomeActivity($id);
        $activity->approve = $type;
        $activity->save();
    }

    public function filterIncomeActivity($request)
    {
        $activities = $this->findIncomeActivities($request->organisation_id);
        if(!is_null($request->payment_item_id)) {
            $activities = $activities->where('income_activities.payment_item_id', $request->payment_item_id);
        }
        if(!is_null($request->status) && $request->status != "ALL") {
            $activities = $activities->where('income_activities.approve', $request->status);
        }
        if(!is_null($request->month)) {
            $activities = $activities ->whereMonth('income_activities.date', $request->month);
        }
        $activities = $activities->orderBy('income_activities.name', 'ASC');

        $total_income = $this->computeTotalIncomeActivities($activities->get());
        $paginated_data = !is_null($request->per_page) ? $activities->paginate($request->per_page) : $activities->get();

        $total = !is_null($request->per_page) ? $paginated_data->total() : count($paginated_data);
        $last_page = !is_null($request->per_page) ? $paginated_data->lastPage(): 0;
        $per_page = !is_null($request->per_page) ? (int)$paginated_data->perPage() : 0;
        $current_page = !is_null($request->per_page) ? $paginated_data->currentPage() : 0;

        return new IncomeActivityCollection($paginated_data,$total_income, $total, $last_page,
            $per_page, $current_page);
    }


    public function generateIncomeActivityPdf()
    {

    }

    private function findIncomeActivity($id)
    {
        return IncomeActivity::findOrFail($id);
    }

    private function findIncomeActivities($organisation_id)
    {
        $current_session = $this->sessionService->getCurrentSession();
        return IncomeActivity::select('income_activities.*')
                            ->join('organisations', ['organisations.id' => 'income_activities.organisation_id'])
                            ->where('income_activities.organisation_id', $organisation_id)
                            ->where('income_activities.session_id', $current_session->id);
    }

    public function calculateTotal($activities)
    {
        $total = 0;
        foreach($activities as $activity){
            $total += $activity->amount;
        }

        return $total;
    }

    public function getQuarterlyIncomeActivities($quarter_num, $current_year): array
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];

        return  DB::table('income_activities')
            ->join('payment_items', 'payment_items.id', '=', 'income_activities.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'income_activities.session_id')
            ->where('income_activities.approve', PaymentStatus::APPROVED)
            ->whereBetween('income_activities.created_at', [$start_quarter, $end_quarter])
            ->select('income_activities.id', 'income_activities.name', 'income_activities.amount', 'sessions.year')
            ->orderBy('name')
            ->get()->toArray();
    }

    public function getYearIncomeActivities($year): array
    {

        return  DB::table('income_activities')
            ->join('payment_items', 'payment_items.id', '=', 'income_activities.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'income_activities.session_id')
            ->where('income_activities.approve', PaymentStatus::APPROVED)
            ->where('income_activities.session_id', $year)
            ->select('income_activities.id', 'income_activities.name', 'income_activities.amount', 'sessions.year')
            ->orderBy('name')
            ->get()->toArray();
    }

    public function getIncomePerActivity($id): Collection
    {
        return DB::table('income_activities')
            ->join('payment_items', 'payment_items.id', '=', 'income_activities.payment_item_id')
            ->where('income_activities.payment_item_id', $id)
            ->where('income_activities.approve', PaymentStatus::APPROVED)
            ->select('income_activities.name', 'income_activities.amount')
            ->orderBy('income_activities.name')
            ->get();
    }
    private function getPaymentItemId($id){
        $item = PaymentItem::findOrFail($id);
        return $item->id;
    }
}

