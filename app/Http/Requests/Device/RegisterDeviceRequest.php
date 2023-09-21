<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class RegisterDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'device_model_id' => [
                'required',
                'numeric',
                'exists:device_models,id'
            ],
            'color' => [
                'required',
                'max:255'
            ],
            'access_key' => [
                'required',
                'digits:44',
                'unique:invoices,access_key'
            ],
            'imei_1' => [
                'required',
                'digits:15',
                'different:imei_2',
                'unique:devices,imei_1',
                'unique:devices,imei_2'
            ],
            'imei_2' => [
                'required',
                'digits:15',
                'unique:devices,imei_1',
                'unique:devices,imei_2'
            ]
        ];
    }
}
