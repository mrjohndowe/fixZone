<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SettingController;
use App\Http\Requests\WAGatewayRequest;
use Illuminate\Http\JsonResponse;

class WAGatewaysController extends SettingController
{
    /**
     * Construct middleware and initialize master app settings
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('demo')->only(
            [
                'setWaApi',
            ]
        );
        $this->settings = $this->master();
        $this->collection = collect($this->settings);
    }

    /**
     * Gets the wa api.
     *
     * @return     JsonResponse  The wa api.
     */
    public function getWaApi(): JsonResponse
    {
        return response()->json(
            $this->collection->only(
                [
                    'wa_status',
                    'wa_twilio_account_sid',
                    'wa_twilio_auth_token',
                    'wa_twilio_from',
                ]
            )
        );
    }

    /**
     * Sets the wa api.
     *
     * @param      \App\Http\Requests\WAGatewayRequest  $braintree  The braintree
     *
     * @return     JsonResponse                          The json response.
     */
    public function setWaApi(WAGatewayRequest $sms): JsonResponse
    {
        $this->settings->update($sms->validated());
        return response()->json(
            ['message' => __('Settings updated successfully')]
        );
    }
}
