<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_active === false) {
            $user->tokens()->delete();

            return response()->json(['message' => 'এই অ্যাকাউন্টটি নিষ্ক্রিয় করা হয়েছে।'], 403);
        }

        return $next($request);
    }
}
