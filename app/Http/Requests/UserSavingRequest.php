<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSavingRequest extends FormRequest
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
            'amount_deposited'      =>'required|min:1',
            'comment'               =>'required|string|max:5000',
            'user_id'               =>'required|string',
            'id'                    =>''
        ];
    }
}
