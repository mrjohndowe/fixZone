<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WAGatewayRequest extends FormRequest
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
        $rules = [
            'wa_status' => 'required',
        ];
        if (request()->has('wa_status')) {
            $rules['wa_twilio_account_sid'] = 'required';
            $rules['wa_twilio_auth_token'] = 'required';
            $rules['wa_twilio_from'] = 'required';
        }
        return $rules;
    }
}
