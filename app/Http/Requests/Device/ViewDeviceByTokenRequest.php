<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\ApiFormRequest;
use App\Models\DeviceSharingToken;

class ViewDeviceByTokenRequest extends ApiFormRequest
{
    /**
     * Validates the token field and returns an instance of DeviceSharingToken.
     */
    public function deviceSharingToken(): DeviceSharingToken
    {
        return DeviceSharingToken::where([
            'token' => $this->token,
        ])->first();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required',
                'digits:8',
                'exists:device_sharing_tokens,token',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'token.exists' => trans('validation.custom.token.exists'),
        ];
    }
}
