<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkPaymentRequest extends FormRequest
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
                'row.*.user_id'         => 'required|string',
                'row.*.payment_item_id' => 'required|string',
                'row.*.comment'         => 'string|required',
                'row.*.year'            => 'required|string',
                'row.*.amount_deposited'=> 'required|numeric',
                'row.*.code'            => 'required|string',
                'row.*.is_compulsory'   => 'required|string',
                'row.*.month_name'      => '',
                'row.*.quarterly_name'  => '',
                'row.*.registration_id' => 'required|string',
                'row.*.date'            => 'required|string'
        ];
    }
}
