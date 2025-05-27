<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonRequest
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {

            if (!$request->isJson()) {
                return response()->json([
                    'message' => 'O Content-Type deve ser application/json'
                ], 415);
            }

            if (empty($request->getContent())) {
                return response()->json([
                    'message' => 'O corpo da requisição não pode estar vazio'
                ], 400);
            }

            json_decode($request->getContent());
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'JSON inválido no corpo da requisição',
                    'error' => json_last_error_msg()
                ], 400);
            }
        }

        return $next($request);
    }
}