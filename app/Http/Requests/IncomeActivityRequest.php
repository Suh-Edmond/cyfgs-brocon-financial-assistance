<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomeActivityRequest extends FormRequest
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
            'name'                  => 'required|max:255',
            'description'           => 'max:5000',
            'venue'                 => 'required|string',
            'date'                  => 'required|date',
            'amount'                => 'required|numeric|min:1',
            'payment_item_id'       => 'required|string',
        ];
    }
}
