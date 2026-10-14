<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingTemplatesRequest extends FormRequest
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
            'template_order_created' => 'required',
            'template_order_updated' => 'required',
            'template_order_assigned' => 'required',
            'template_order_alert_to_admin' => 'required',
            'template_order_dispatch' => 'required',
        ];
    }
}
