<?php

namespace App\Traits;

use App\Constants\PaymentItemFrequency;
use App\Constants\PaymentItemType;
use App\Constants\PaymentStatus;
use App\Constants\Roles;
use App\Http\Resources\ExpenditureDetailResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait HelpTrait {
    public static function generateCode($size): string
    {
        $code  = "";
        for($i = 0; $i < $size; $i++){
            $code = $code.rand(0, 9);
        }
        return $code;
    }


    public static function convertBooleanValue($value): bool
    {
        if ($value == 0) {
            $response = false;
        } else {
            $response = true;
        }

        return $response;
    }


    public static function generateResponseForExpenditureDetails($details)
    {
        $detail_response = array();
        foreach ($details as $detail){
            array_push($detail_response, new ExpenditureDetailResource($detail, ($detail->amount_given - $detail->amount_spent)));
        }
        return $detail_response;
    }

    public static function checkExpenditureItemCanBeApproveDeclined($details){
        return collect($details)->filter(function ($detail){
            return $detail -> approve == PaymentStatus::PENDING;
        })->first();
    }

    public static function computeTotalExpensesDetailsByExpenditureCategory($expenses){
        $expenseCollection = collect($expenses);
        $total_given = $expenseCollection->map(function ($expense) {
            return $expense->total_amount_given;
        })->sum();
        $total_spent = $expenseCollection->map(function ($expense) {
            return $expense->total_amount_spent;
        })->sum();
        $total_balance = $expenseCollection->map(function ($expense) {
            return $expense->total_balance;
        })->sum();

        return [$total_given, $total_spent, $total_balance];
    }


    public static function calculateExpenditureBalance($expenditure_detail)
    {
        return $expenditure_detail->amount_given - $expenditure_detail->amount_spent;
    }

    private function calculateExpenditureBalanceByExpenditureItem($amount_given, $total_amount_spent, $total_item_amount): int
    {
        return (($total_item_amount - $amount_given) + ($amount_given - $total_amount_spent));
    }

    private function calculateTotalAmountGiven($expenditure_details): int
    {
        return collect($expenditure_details)->map(function ($detail) {
            return $detail->amount_given;
        })->sum();
    }

    private function calculateTotalAmountSpent($expenditure_details): int
    {
        return collect($expenditure_details)->map(function ($detail) {
            return $detail->amount_spent;
        })->sum();
    }

    public static function getAppName()
    {
        return config('app.name');
    }


    public static function getOrganisationAdministrators()
    {
        $admins = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.name', 'users.telephone', 'users.email', 'roles.name as role_name')
            ->whereIn('roles.name', [Roles::PRESIDENT, Roles::FINANCIAL_SECRETARY, Roles::TREASURER])
            ->get()->toArray();
        $president         = collect($admins)->filter(function ($admin) {return $admin->role_name == Roles::PRESIDENT;});
        $fin_sec           = collect($admins)->filter(function ($admin) {return $admin->role_name == Roles::FINANCIAL_SECRETARY;});
        $treasurer         = collect($admins)->filter(function ($admin) {return $admin->role_name == Roles::TREASURER;});

        return array(Roles::PRESIDENT => $president, Roles::FINANCIAL_SECRETARY => $fin_sec, Roles::TREASURER => $treasurer);
    }

    public static function computeTotalAmountByPaymentCategory($items): int
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item->amount;
        }

        return $total;
    }

    public function computeTotalContribution($contributions)
    {
        $total = 0;
        foreach ($contributions as $contribution) {
            $total += $contribution->amount_deposited;
        }

        return $total;
    }

    public function computeTotalOrganisationContribution($contributions){
        return collect($contributions->get())->map(function ($contribution) {
            return $contribution->total_amount_deposited;
        })->sum();
    }

    public function  saveUserRole($user, $role, $updated_by)
    {
        DB::table('model_has_roles')->insert([
            'role_id'       => $role->id,
            'model_id'      => $user->id,
            'model_type'    => 'App\Models\User',
            'updated_by'    => $updated_by,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now()
        ]);
    }

    public function setOrganisationTelephone($telephone): string
    {

        $numbers = explode("/", $telephone);

        if($numbers[1] != "null"){
            $telephone = $numbers[0]."/".$numbers[1];
        }else{
            $telephone =$numbers[0];
        }

        return $telephone;
    }

    public function  convertStatusToNumber($status): int
    {
        $statuses = $this->getStatuses();

        return $statuses[$status];
    }

    private function getStatuses(): array
    {
        return ['APPROVED' => 1, 'UNAPPROVED' => 0, 'ALL' => 2];
    }

    public function  calculateOrganisationTotalSavings($savings): int
    {
        return collect($savings)->filter(function ($saving){
           return ($saving->approve == PaymentStatus::APPROVED);
       })->map(function ($saving){
           return $saving->total_amount;
        })->sum();
    }

    public function computeTotalIncomeActivities($incomeActivities){
        return collect($incomeActivities)->filter(function ($income){
            return ($income->approve == PaymentStatus::APPROVED) || ($income->approve == PaymentStatus::PENDING);
        })->map(function ($approve_income){
            return $approve_income->amount;
        })->sum();
    }

    public function computeTotalSponsorship($sponsorships){
        return collect($sponsorships)->filter(function ($sponsorship){
            return ($sponsorship->approve == PaymentStatus::APPROVED) || ($sponsorship->approve == PaymentStatus::PENDING);
        })->map(function ($collected_sponsorship){
            return $collected_sponsorship->amount_deposited;
        })->sum();
    }


    private  function  months(): array
    {
        return [
        "January" => 01,
        "February" => 02,
        "March" => 03,
        "April" => 04,
        "May" => 05,
        "June" => 06,
        "July" => 07,
        "August" => 8,
        "September" => 9,
        "October" => 10,
        "November" => 11,
        "December" => 12,

        ];
    }

    public function convertMonthNameToNumber($month): int
    {
        return $this->months()[$month];
    }

    private function convertNumberToMonth($num){
         $months = [
            1 => "January",
            2 => "February",
            3 =>"March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 =>"December",

        ];
         return $months[$num];
    }

    private function convertQuarterNameToNumber($name)
    {
        $quarters = [
         "January - March" => 1 ,
         "April - June" => 2 ,
         "July - September" => 3 ,
         "October - December" => 4
        ];
        return $quarters[$name];
    }

    private function convertNumberToQuarterName($num)
    {
        $quarters = [
            1 => "January - March",
            2 => "April - June",
            3 => "July - September",
            4 =>"October - December"
        ];
        return $quarters[$num];
    }

    public function  calculateTotal($data): int
    {
        $total = 0;
        foreach ($data as $income) {
            $total += $income->amount;
        }

        return $total;
    }

    public function  calculateBalance($data): int
    {
        $total = 0;
        foreach ($data as $income) {
            $total += $income->balance;
        }

        return $total;
    }

    public function getReferenceResource($references)
    {
        $data = [];
        if(str_contains($references, "/")){
            $reference_array = explode("/", $references);
            foreach ($reference_array as $reference){
                if(!empty($reference)){
                    $resource = User::find($reference);
                    array_push($data, new UserResource($resource, null, null));
                }
            }
        }else {
           if(!empty($references)){
               $resource = User::find($references);
               array_push($data, new UserResource($resource, null, null));
           }
        }
        return $data;
    }

    public function getDateQuarter($item_frequency, $item_created_at)
    {
        $quarter = null;
        if($item_frequency == PaymentItemFrequency::QUARTERLY){
            $a = Carbon::parse($item_created_at);
            $quarter = $this->convertNumberToQuarterName($a->quarter);
        }
        return $quarter;
    }

    public function getItemMonth($item_frequency, $item_created_at)
    {
        $itemMonth= "";
        if($item_frequency == PaymentItemFrequency::MONTHLY){
            $itemMonth = Carbon::parse($item_created_at);
        }
        return $itemMonth;
    }

    public function getQuarters(){
        return array("January - March",
            "April - June",
            "July - September",
            "October - December");
    }

    public function getMonths(){
        return
            array("January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December");
    }

    public function checkMemberExistAsReference($user_id, $reference)
    {
        if (str_contains($reference, "/")){
            $reference_array = explode("/", $reference);
            if (in_array($user_id, $reference_array)){
                return true;
            }else{
                return  false;
            }
        }else{
            return $user_id == trim($reference);
        }
    }

    public static function checkMemberIsAdministrator($user_id)
    {
        return DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('users.id', $user_id)
            ->whereIn('roles.name', [Roles::TREASURER, Roles::FINANCIAL_SECRETARY, Roles::PRESIDENT, Roles::AUDITOR])
            ->select('users.*')
            ->first();
    }

    public static function checkMemberNotAdministrator($user_id)
    {
        return DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('users.id', $user_id)
            ->whereNotIn('roles.name', [Roles::TREASURER, Roles::FINANCIAL_SECRETARY, Roles::PRESIDENT, Roles::AUDITOR])
            ->select('users.*')
            ->first();
    }

    public static function getAllAdminsId()
    {
        $adminRef = null;
        $adminId = DB::table('users')
                    ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->select('users.id')
                    ->whereIn('roles.name', [Roles::TREASURER, Roles::FINANCIAL_SECRETARY, Roles::PRESIDENT, Roles::AUDITOR])
                    ->get()->toArray();
        foreach ($adminId as $id){
            $adminRef .= $id->id ."/";
        }
        return $adminRef;
    }

    public static function getAllNoAdminsId()
    {
        $nonAdminRef = null;
        $nonAdminId = User::all();
        foreach ($nonAdminId as $user){
            if (count($user->roles) <= 1){
                $nonAdminRef .= $user->id ."/";
            }
        }
        return $nonAdminRef;
    }

    public static function getStartQuarter($year, $quarter)
    {
        $start_date = "";
        $end_date = "";
        switch ($quarter){
            case 1:
                $start_date = Carbon::create($year, 2)->startOfQuarter();
                $end_date = Carbon::create($year, 2)->endOfQuarter();
            break;
            case 2:
                $start_date = Carbon::create($year, 6)->startOfQuarter();
                $end_date = Carbon::create($year, 6)->endOfQuarter();
            break;
            case 3:
                $start_date = Carbon::create($year, 9)->startOfQuarter();
                $end_date = Carbon::create($year, 9)->endOfQuarter();
            break;
            case 4:
                $start_date = Carbon::create($year, 12)->startOfQuarter();
                $end_date = Carbon::create($year, 12)->endOfQuarter();
            break;
        }

        return [$start_date->toDateTimeString(),  $end_date->toDateTimeString()];
    }

    public function getPaymentItemQuartersBySession($item_frequency, $item_created_at)
    {
        $quarters = $this->getQuarters();
        $current_quarter = $this->convertQuarterNameToNumber($this->getDateQuarter($item_frequency, $item_created_at));
        return array_splice($quarters, ($current_quarter - 1), count($quarters));
    }

    private function getPaymentItemMonthsBySession($item_frequency, $item_created_at)
    {
        $all_months = $this->getMonths();
        $current_month_index = $this->getItemMonth($item_frequency, $item_created_at)->month;
        return array_splice($all_months, $current_month_index - 1, count($all_months));
    }

    public function computeTotalPaymentItemAmount($payment_item){
        $total_payment_item_amount = $payment_item->amount;
        if($payment_item->frequency == PaymentItemFrequency::QUARTERLY){
            $total_payment_item_amount *= count($this->getPaymentItemQuartersBySession($payment_item->frequency, $payment_item->created_at));
        }
        if($payment_item->frequency == PaymentItemFrequency::MONTHLY){
            $total_payment_item_amount *= count($this->getPaymentItemMonthsBySession($payment_item->frequency, $payment_item->created_at));
        }
        return $total_payment_item_amount;
    }

    public function getTotalPaymentItemAmountByQuarters($item)
    {
        if($item->frequency == PaymentItemFrequency::QUARTERLY){
            $quarters = $this->getPaymentItemQuartersBySession($item->frequency, $item->created_at);
            $total_amount = count($quarters) * $item->amount;
        }
        else if($item->frequency == PaymentItemFrequency::MONTHLY){
            $months = $this->getPaymentItemMonthsBySession($item->frequency, $item->created_at);
            $total_amount = count($months) * $item->amount;
        }else {
            $total_amount = $item->amount;
        }

        return $total_amount;
    }

    public function computeTotalExpectedPaymentItemAmount($payment_item) {
        $member_size = 1;
        switch ($payment_item->frequency) {
            case PaymentItemFrequency::ONE_TIME:
            case PaymentItemFrequency::YEARLY:
                switch ($payment_item->type){
                    case PaymentItemType::A_MEMBER:
                        $amount = $payment_item->amount;
                        $member_size = 1;
                        break;
                    case PaymentItemType::ALL_MEMBERS:
                        $member_size = User::all()->count();
                        $amount = $member_size * $payment_item->amount;
                        break;
                    case PaymentItemType::GROUPED_MEMBERS:
                        $member_size = count(explode("/", $payment_item->reference));
                        $amount = $member_size * $payment_item->amount;
                        break;
                    case PaymentItemType::MEMBERS_WITH_ROLES:
                        $member_size = count(explode("/", $this->getAllAdminsId()));
                        $amount = $member_size * $payment_item->amount;
                        break;
                    case PaymentItemType::MEMBERS_WITHOUT_ROLES:
                        $member_size = count(explode("/", $this->getAllNoAdminsId()));
                        $amount = $member_size * $payment_item->amount;
                        break;
                    default:
                        $amount = $payment_item->amount;
                }
            break;
            case PaymentItemFrequency::QUARTERLY:
            case PaymentItemFrequency::MONTHLY:
                $total_payable_months = $this->getTotalPaymentItemAmountByQuarters($payment_item);
                switch ($payment_item->type){
                    case PaymentItemType::A_MEMBER:
                        $member_size = 1;
                        $amount = $total_payable_months;
                        break;
                    case PaymentItemType::ALL_MEMBERS:
                        $member_size = User::all()->count();
                        $amount = $member_size * $total_payable_months;
                        break;
                    case PaymentItemType::GROUPED_MEMBERS:
                        $member_size = count(explode("/", $payment_item->reference));
                        $amount = $member_size * $total_payable_months;
                        break;
                    case PaymentItemType::MEMBERS_WITH_ROLES:
                        $member_size = count(explode("/", $this->getAllAdminsId()));
                        $amount = $member_size * $total_payable_months;
                        break;
                    case PaymentItemType::MEMBERS_WITHOUT_ROLES:
                        $member_size = count(explode("/", $this->getAllNoAdminsId()));
                        $amount = $member_size * $total_payable_months;
                        break;
                    default:
                        $amount = $total_payable_months;
                }
            break;
            default:
                $amount = 0;
        }
        return [$amount, $member_size];
    }

    public function computeTotalBalanceByPaymentItem($payment_item, $total_amount_contributed) {
        return $this->computeTotalExpectedPaymentItemAmount($payment_item)[0] - $total_amount_contributed;
    }

    public function computePercentageContributed($total_amount_contributed, $expected_amount){
        return ($total_amount_contributed/$expected_amount) * 100;
    }

    public function computeTotalAmountByUser($array) {
        $total = 0;
        foreach ($array as $item){
            $total += $item->total_amount_deposited;
        }
        return $total;
    }

    public function computeBalanceByUser($array) {
        $total = 0;
        foreach ($array as $item){
            $total += $item->balance;
        }
        return $total;
    }

    public function generatePaswordResetToken(){
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pin = mt_rand(1000000, 9999999)
            . mt_rand(1000000, 9999999)
            . $characters[rand(0, strlen($characters) - 1)];
        return substr(str_shuffle($pin), 0, 7);
    }

}
