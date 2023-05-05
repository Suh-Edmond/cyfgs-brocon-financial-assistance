<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentItemRequest extends FormRequest
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
            'name'          => 'required|max:255',
            'amount'        => 'required',
            'compulsory'    => 'required|boolean',
            'description'   => 'max:4000',
            'type'          => 'required|string',
            'frequency'     => 'required|string',
            'reference'     => 'string'
        ];
    }
}
