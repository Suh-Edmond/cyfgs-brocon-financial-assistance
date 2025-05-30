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
            'name'            => 'required|max:255',
            'email'           => 'required|email|unique:users,email',
            'telephone'       => 'required|string|unique:users,telephone',
            'password'        => 'string',
            'address'         => 'string|required',
            'occupation'      => 'string|required',
            'gender'          => 'string|required',
        ];
    }
}
