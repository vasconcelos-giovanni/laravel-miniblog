<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class EnsureMetadataMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            $data['meta'] = array_merge(
                $data['meta'] ?? [],
                Context::all(),
                ['timestamp' => now()->toIso8601String()]
            );

            $response->setData($data);
        }

        return $response;
    }
}
