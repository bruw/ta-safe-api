<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\BaseFormRequest;

class ValidateDeviceRegistrationRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('validateDeviceRegistration', $this->device);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cpf' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'products' => ['required', 'string', 'max:16000'],
        ];
    }
}
