<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use App\Rules\AttributeCannotBeBoolean;

class CreateDeviceTransferRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('createDeviceTransfer', $this->device);
    }

    /**
     * Validates the target_user field and returns an instance of User.
     */
    public function targetUser(): User
    {
        return User::find($this->target_user_id);
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
                new AttributeCannotBeBoolean,
            ],
        ];
    }
}
