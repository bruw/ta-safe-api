<?php

namespace App\Http\Requests\Device;

use App\Rules\AttributeCannotBeBoolean;
use Illuminate\Foundation\Http\FormRequest;

class CreateDeviceTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('createDeviceTransfer', $this->device);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'target_user_id' => [
                'required',
                'numeric',
                'exists:users,id',
                new AttributeCannotBeBoolean
            ]
        ];
    }
}
