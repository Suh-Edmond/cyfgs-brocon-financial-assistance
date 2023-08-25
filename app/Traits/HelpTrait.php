<?php

namespace App\Traits;

use App\Constants\PaymentItemFrequency;
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
        $current_year = date("Y");
        for($i = 0; $i < $size; $i++){
            $code = $code.rand(0, 9);
        }
        $code = $code.$current_year;

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


    public static function generateResponseForExpenditureDetails($details): array
    {
        $response = [];

        foreach ($details as $detail) {
            $balance = HelpTrait::calculateExpenditureBalance($detail);
            array_push($response, new ExpenditureDetailResource($detail, $balance));
        }

        return $response;
    }


    public static function calculateExpenditureBalance($expenditure_detail)
    {
        return $expenditure_detail->amount_given - $expenditure_detail->amount_spent;
    }

    private function calculateExpenditureBalanceByExpenditureItem($expenditure_details, $total_item_amount): int
    {
        $total_amount_given = $total_item_amount - $this->calculateTotalAmountGiven($expenditure_details);
        $total_balance = $this->calculateTotalAmountGiven($expenditure_details) - $this->calculateTotalAmountSpent($expenditure_details);
        return ($total_amount_given + $total_balance);
    }

    private function calculateTotalAmountGiven($expenditure_details): int
    {

        $total = 0;
        foreach ($expenditure_details as $expenditure_detail) {
            $total += $expenditure_detail->amount_given;
        }

        return $total;
    }

    private function calculateTotalAmountSpent($expenditure_details): int
    {
        $total = 0;
        foreach ($expenditure_details as $expenditure_detail) {
            $total += $expenditure_detail->amount_spent;
        }

        return $total;
    }

    public static function getAppName()
    {
        return config('app.name');
    }


    public static function getOrganisationAdministrators()
    {
        return DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.name', 'users.telephone', 'users.email')
            ->whereIn('roles.name', [Roles::PRESIDENT, Roles::FINANCIAL_SECRETARY, Roles::TREASURER])
            ->get()->toArray();
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

    public function  saveUserRole($user, $role, $updated_by)
    {
        DB::table('model_has_roles')->insert([
            'role_id'       => $role->id,
            'model_id'      => $user->id,
            'model_type'    => 'App\Models\User',
            'updated_by'    => $updated_by
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
           return $saving->approve == PaymentStatus::APPROVED;
       })->map(function ($saving){
           return $saving->total_amount;
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

    public function getDateQuarter($item)
    {
        $quarter = null;
        if($item->frequency == PaymentItemFrequency::QUARTERLY){
            $a = Carbon::parse(Carbon::now());
            $quarter = $this->convertNumberToQuarterName($a->quarter);
        }
        return $quarter;
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
        if($quarter == 1){
            $start_date = Carbon::create($year, 2)->startOfQuarter();
            $end_date = Carbon::create($year, 2)->endOfQuarter();
        }elseif ($quarter == 2){
            $start_date = Carbon::create($year, 5)->startOfQuarter();
            $end_date = Carbon::create($year, 5)->endOfQuarter();
        }elseif ($quarter = 4) {
            $start_date = Carbon::create($year, 8)->startOfQuarter();
            $end_date = Carbon::create($year, 8)->endOfQuarter();
        }else {
            $start_date = Carbon::create($year, 11)->startOfQuarter();
            $end_date = Carbon::create($year, 11)->endOfQuarter();
        }

        return [$start_date->toDateTimeString(),  $end_date->toDateTimeString()];
    }
}
