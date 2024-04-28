<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\BaseFormRequest;

class DeviceRegistrationValidationRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        // verificar se é o proprietário
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
            'cpf' => ['required', 'string'],
            'name' => ['required', 'string'],
            'products' => ['required', 'string']
        ];
    }
}
