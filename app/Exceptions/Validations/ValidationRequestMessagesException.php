<?php

namespace App\Exceptions\Validations;

use App\Http\Messages\FlashMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ValidationRequestMessagesException extends ValidationException
{
    protected FlashMessage $flashMessage;

    public function __construct($validator, $response = null, $errorBag = 'default')
    {
        $this->flashMessage = FlashMessage::error(__('messages.errors'));
        parent::__construct($validator, $response, $errorBag);
    }

    public function setFlashMessage(FlashMessage $msg = null): ValidationRequestMessagesException
    {
        $this->flashMessage = $msg;

        return $this;
    }

    public function render(Request $request): JsonResponse
    {
        $response = $this->flashMessage->toArray($request);
        $response['errors'] = $this->validator->errors()->getMessages();

        return new JsonResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}