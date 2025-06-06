<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrganisationRequest extends FormRequest
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
            'email'         => 'email',
            'address'       => 'required',
            'box_number'    => 'integer',
            'description'   => 'required|max:5000',
            'salutation'    => 'string',
            'region'        => 'string',
            'telephone'     => 'required',//should be a string separated by /
            'id'            => ''
        ];
    }
}
