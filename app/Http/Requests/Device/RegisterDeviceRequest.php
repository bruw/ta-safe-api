<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\ApiFormRequest;

class RegisterDeviceRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'device_model_id' => [
                'bail',
                'required',
                'integer',
                'exists:device_models,id',
            ],
            'color' => [
                'bail',
                'required',
                'max:255',
            ],
            'access_key' => [
                'bail',
                'required',
                'digits:44',
                'unique:invoices,access_key',
            ],
            'imei_1' => [
                'bail',
                'required',
                'digits:15',
                'different:imei_2',
                'unique:devices,imei_1',
                'unique:devices,imei_2',
            ],
            'imei_2' => [
                'bail',
                'required',
                'digits:15',
                'unique:devices,imei_1',
                'unique:devices,imei_2',
            ],
        ];
    }
}
