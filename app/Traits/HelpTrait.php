<?php

namespace App\Traits;

use App\Http\Resources\ExpenditureDetailResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HelpTrait {

    public static function convertBooleanValue($value)
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
        $response = array();

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

    private function calculateExpenditureBalanceByExpenditureItem($expenditure_details, $total_item_amount)
    {
        $balance = $total_item_amount - $this->calculateTotalAmountGiven($expenditure_details);
        $balance += $this->calculateTotalAmountGiven($expenditure_details) - $this->calculateTotalAmountSpent($expenditure_details);

        return $balance;
    }

    private function calculateTotalAmountGiven($expenditure_details)
    {
        $total = 0;
        foreach ($expenditure_details as $expenditure_detail) {
            $total += $expenditure_detail->amount_given;
        }

        return $total;
    }

    private function calculateTotalAmountSpent($expenditure_details)
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


    public static function getOrganisationAdministrators($role)
    {
        return DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'userS.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.*')
            ->where('roles.name', $role)
            ->first();
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

    public function  saveUserRole($user, $role)
    {
        DB::table('model_has_roles')->insert([
            'role_id'       => $role->id,
            'model_id'      => $user->id,
            'model_type'    => 'App\Models\User',
            'updated_by'    => is_null(auth::user()) ? $user->name: User::find(Auth::user()['id'])->name
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

    public function  calculateOrganisationTotalSavings($savings) {
        $total = 0;
        foreach ($savings as $saving) {
            $total += $saving->total_amount_deposited;
        }

        return $total;
    }
}
