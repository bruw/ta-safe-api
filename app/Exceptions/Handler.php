<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            return response()->json([
                'message' => trans('auth.unauthenticated')
            ], Response::HTTP_UNAUTHORIZED);
        });

        $this->renderable(function (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                return response()->json([
                    'message' => trans('auth.unauthorized')
                ], Response::HTTP_FORBIDDEN);
            }
        });

        $this->renderable(function (HttpException $e) {
            if ($e->getStatusCode() === 404) {
                return response()->json([
                    'message' => trans('validation.not_found')
                ], Response::HTTP_NOT_FOUND);
            }
        });
    }
}
