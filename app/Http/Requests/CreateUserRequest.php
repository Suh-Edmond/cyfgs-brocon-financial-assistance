<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name'       => 'required|max:255',
            'email'      => 'email|unique:users,email',
            'telephone'  => 'required|string|size:9',
            'password'   => 'required|confirmed|min:8',
            'address'    => 'string',
            'occupation' => 'string',
            'gender'     => 'string',
            'organisation_id' => 'integer'
        ];
    }
}
