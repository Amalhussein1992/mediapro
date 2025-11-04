<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم التسجيل بنجاح',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * تسجيل الدخول
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * حذف جميع الـ tokens للمستخدم
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج من جميع الأجهزة',
        ]);
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي',
            'user' => $request->user(),
        ]);
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['كلمة المرور الحالية غير صحيحة'],
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح',
        ]);
    }

    /**
     * إرسال OTP إلى رقم الهاتف
     */
    public function sendOTP(Request $request, FirebaseService $firebase): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
        ]);

        $result = $firebase->sendOTP($validated['phone']);

        if (!$result['success']) {
            return response()->json([
                'message' => $result['error'],
            ], 400);
        }

        return response()->json([
            'message' => $result['message'],
            'otp' => $result['otp'] ?? null, // فقط في التطوير
        ]);
    }

    /**
     * التحقق من OTP
     */
    public function verifyOTP(Request $request, FirebaseService $firebase): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        $result = $firebase->verifyOTP($validated['phone'], $validated['code']);

        if (!$result['success']) {
            return response()->json([
                'message' => $result['error'],
            ], 400);
        }

        // البحث عن المستخدم أو إنشاؤه
        $user = User::firstOrCreate(
            ['phone' => $validated['phone']],
            [
                'name' => 'User ' . substr($validated['phone'], -4),
                'email' => $validated['phone'] . '@phone.temp',
                'password' => Hash::make(str()->random(32)),
            ]
        );

        // إنشاء token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم التحقق بنجاح',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * التحقق من إمكانية إعادة إرسال OTP
     */
    public function canResendOTP(Request $request, FirebaseService $firebase): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
        ]);

        $result = $firebase->canResend($validated['phone']);

        return response()->json($result);
    }

    /**
     * الحصول على إعدادات Firebase للـ Frontend
     */
    public function getFirebaseConfig(FirebaseService $firebase): JsonResponse
    {
        $config = $firebase->getFirebaseConfig();

        if (empty($config)) {
            return response()->json([
                'message' => 'Firebase غير مفعّل',
                'enabled' => false,
            ]);
        }

        return response()->json([
            'enabled' => true,
            'config' => $config,
        ]);
    }
}
