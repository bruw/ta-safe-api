<?php

namespace App\Http\Requests;

use App\Exceptions\Validations\ValidationRequestMessagesException;
use App\Http\Messages\FlashMessage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw (new ValidationRequestMessagesException($validator))
            ->setFlashMessage($this->flashMessage())
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    public function authorize(): bool
    {
        return true;
    }

    public function flashMessage(): FlashMessage
    {
        return FlashMessage::error(trans('flash_messages.errors'));
    }
}