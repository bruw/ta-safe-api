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
            ]
        ];
    }
}
