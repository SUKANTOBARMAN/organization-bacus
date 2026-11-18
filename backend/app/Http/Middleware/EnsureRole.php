<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /** ব্যবহার: ->middleware('role:super_admin,admin') */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isRole(...$roles)) {
            return response()->json(['message' => 'এই কাজের অনুমতি আপনার নেই।'], 403);
        }

        return $next($request);
    }
}
