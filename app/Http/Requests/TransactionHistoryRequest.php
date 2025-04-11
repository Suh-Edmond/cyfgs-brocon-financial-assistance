<?php

namespace App\Http\Requests;

use App\Constants\RegistrationFrequency;
use App\Constants\TransactionDataGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_amount_deposited'   => 'required',
            'new_amount_deposited'   => 'required',
            'reason'                 => 'required|string|max:1000',
            'reference_data'         => 'required|string',
            'transaction_data_group' => ['required', Rule::in([TransactionDataGroup::EXPENDITURE_ITEM_DETAILS, TransactionDataGroup::EXPENDITURE_ITEMS, TransactionDataGroup::INCOME_ACTIVITY, TransactionDataGroup::SPONSORSHIP,
                TransactionDataGroup::USER_CONTRIBUTIONS, TransactionDataGroup::USER_REGISTRATION, TransactionDataGroup::USER_SAVING])]
        ];
    }
}
