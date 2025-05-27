<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Os dados fornecidos são inválidos.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() && $e instanceof \JsonException) {
                return response()->json([
                    'message' => 'JSON inválido no corpo da requisição',
                    'error' => $e->getMessage()
                ], 400);
            }
        });
    }
}
