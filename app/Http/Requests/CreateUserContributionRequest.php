<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserContributionRequest extends FormRequest
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
            'amount_deposited'          => 'required|numeric',
            'comment'                   => 'max:4000',
            'user_id'                   => 'required|string',
            'payment_item_id'           => 'required|string',
            'year'                      => 'required|string'
        ];
    }
}
