<?php

namespace App\Exceptions;

use App\Http\Messages\FlashMessage;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AuthenticationException $e) {
            return response()->json(
                FlashMessage::error(trans('http_exceptions.unauthenticated')),
                Response::HTTP_UNAUTHORIZED
            );
        });

        $this->renderable(function (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                return response()->json(
                    FlashMessage::error(trans('http_exceptions.unauthorized')),
                    $e->getStatusCode()
                );
            }

            if ($e->getStatusCode() === 404) {
                return response()->json(
                    FlashMessage::error(trans('http_exceptions.not_found')),
                    $e->getStatusCode()
                );
            }

            if ($e->getStatusCode() === 429) {
                return response()->json(
                    FlashMessage::error(trans('http_exceptions.too_many_attempts')),
                    $e->getStatusCode()
                );
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof HttpJsonResponseException) {
            return response()->json(
                FlashMessage::error($e->getMessage()),
                $e->getCode()
            );
        }

        return parent::render($request, $e);
    }
}
