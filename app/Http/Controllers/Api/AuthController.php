<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'هذا الحساب معطل.'], 403);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
            // For now, assume one store per user
            'store' => $user->stores()->first(),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'store_name' => 'required|string|max:255',
            'device_name' => 'required|string',
        ]);

        \DB::beginTransaction();
        try {
            // 1. Create User
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'owner',
                'is_active' => true,
            ]);

            // 2. Create Store
            $store = \App\Models\Store::create([
                'user_id' => $user->id,
                'name' => $request->store_name,
                'phone' => $request->phone,
                'currency' => 'IQD',
            ]);

            // 3. Attach User to Store
            $user->stores()->attach($store->id, ['role' => 'owner']);

            // 4. Setup Default Subscription (Free Week)
            $defaultPlan = \App\Models\SubscriptionPlan::where('name', 'Free Week')->first();

            $subscription = \App\Models\Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $defaultPlan?->id,
                'plan_name' => $defaultPlan?->name ?? 'Free Week',
                'start_date' => now(),
                'end_date' => now()->addDays($defaultPlan?->duration_days ?? 7),
                'is_active' => true,
                'payment_status' => 'paid',
                'auto_renew' => true,
            ]);

            \DB::commit();

            return response()->json([
                'token' => $user->createToken($request->device_name)->plainTextToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'role' => $user->role,
                ],
                'store' => $store,
                'subscription' => $subscription,
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء الحساب.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح.']);
    }
}
