<?php

namespace App\Exceptions;

use App\Http\Messages\FlashMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ApiValidationException extends ValidationException
{
    protected FlashMessage $flashMessage;

    public function __construct($validator, $response = null, $errorBag = 'default')
    {
        parent::__construct($validator, $response, $errorBag);
        $this->flashMessage = FlashMessage::error(__('messages.errors'));
    }

    /**
     * Set the FlashMessage instance for the exception.
     */
    public function setFlashMessage(?FlashMessage $msg = null): ApiValidationException
    {
        $this->flashMessage = $msg;

        return $this;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $response = $this->flashMessage->toArray($request);
        $response['errors'] = $this->validator->errors()->getMessages();

        return new JsonResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
