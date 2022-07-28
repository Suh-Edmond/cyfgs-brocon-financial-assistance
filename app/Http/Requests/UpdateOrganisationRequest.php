<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganisationRequest extends FormRequest
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
           'telephone'     => 'required|size:9',
           'email'         => 'email|unique:organisations,email',
           'address'       => 'required',
           'description'   => 'required|max:5000',
           'logo'          => 'string',
        ];
    }
}
