<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // For now, we take the first store. 
            // In a more complex setup, the app might send a header 'X-Store-Id'
            $store = $user->stores()->first();

            if ($store) {
                app()->instance('current_store_id', $store->id);
            }
        }

        return $next($request);
    }
}
