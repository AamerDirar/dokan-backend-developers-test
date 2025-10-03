<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [];

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'statusCode' => Response::HTTP_FORBIDDEN,
                    'message'    => __('messages.unauthorized_action'),
                    'errors'     => [],
                ], Response::HTTP_FORBIDDEN);
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message'    => __('messages.validation_failed'),
                    'errors'     => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message'    => $e->getMessage(),
                    'errors'     => [],
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
