<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role !== 'superadmin') {
            $subscription = $user->subscription;

            if (!$subscription || !$subscription->is_active || $subscription->isExpired()) {
                // Allow GET requests (viewing only)
                if (!$request->isMethod('GET')) {
                    return response()->json([
                        'message' => 'عذراً، يجب تجديد الاشتراك للقيام بهذه العملية.',
                        'subscription_expired' => true
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
