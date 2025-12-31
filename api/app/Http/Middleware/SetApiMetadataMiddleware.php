<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class SetApiMetadataMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName() ?? 'unknown';

        $useCase = str_replace(['.', '-'], ' ', ucwords($routeName, '.-'));

        Context::add('use_case', $useCase);
        Context::add('app_name', config('app.name'));

        return $next($request);
    }
}
