<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Set locale based on request header
        $locale = $request->header('Accept-Language', 'en');
        app()->setLocale($locale);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],
            'password' => 'required|string|min:8',
            'account_type' => 'required|in:individual,business',
        ], [
            'name.required' => $locale === 'ar' ? 'الاسم مطلوب' : 'Name is required',
            'email.required' => $locale === 'ar' ? 'البريد الإلكتروني مطلوب' : 'Email is required',
            'email.email' => $locale === 'ar' ? 'البريد الإلكتروني غير صحيح' : 'Email is invalid',
            'email.unique' => $locale === 'ar' ? 'البريد الإلكتروني مسجل مسبقاً' : 'Email already exists',
            'password.required' => $locale === 'ar' ? 'كلمة المرور مطلوبة' : 'Password is required',
            'password.min' => $locale === 'ar' ? 'كلمة المرور يجب أن تكون 8 أحرف على الأقل' : 'Password must be at least 8 characters',
            'account_type.required' => $locale === 'ar' ? 'نوع الحساب مطلوب' : 'Account type is required',
            'account_type.in' => $locale === 'ar' ? 'نوع الحساب غير صحيح' : 'Invalid account type',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'account_type' => $request->account_type,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
