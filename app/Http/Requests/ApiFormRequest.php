<?php

namespace App\Http\Requests;

use App\Exceptions\ApiValidationException;
use App\Http\Messages\FlashMessage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class ApiFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw (new ApiValidationException($validator))
            ->setFlashMessage($this->flashMessage())
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Generate a flash message for the request.
     */
    public function flashMessage(): FlashMessage
    {
        return FlashMessage::error(trans('flash_messages.errors'));
    }
}
