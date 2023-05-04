<?php

namespace App\Http\Requests;

use App\Constants\SessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SessionRequest extends FormRequest
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
            'year' => 'required|string|unique:sessions,year',
            'status' => 'required',Rule::in([SessionStatus::ACTIVE, SessionStatus::IN_ACTIVE])
        ];
    }
}
