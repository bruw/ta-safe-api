<?php

namespace App\Http\Requests\Device;

use App\Rules\AttributeCannotBeBoolean;
use Illuminate\Foundation\Http\FormRequest;

class ViewDeviceByTokenRequest extends FormRequest
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
            'token' => [
                'required',
                'digits:8',
                'exists:device_sharing_tokens,token',
                new AttributeCannotBeBoolean
            ]
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
            'token.exists' => trans('validation.custom.token.exists')
        ];
    }
}
