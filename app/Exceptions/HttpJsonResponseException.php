<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class HttpJsonResponseException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(): JsonResponse
    {
        return new JsonResponse(['errors' => [
            'message' => $this->getMessage(),
        ], ], $this->getCode());
    }
}
