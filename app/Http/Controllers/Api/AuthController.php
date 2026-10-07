<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin'
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['email'] = strtolower(trim($validated['email']));
        $validated['phone'] = ! empty($validated['phone']) ? trim($validated['phone']) : null;

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role']
            ]);

            $user->makeHidden(['password', 'remember_token']);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Throwable $e) {
            Log::error('User registration failed: ' . $e->getMessage(), [
                'email' => $validated['email'],
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Registration failed'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $validated['email'] = strtolower(trim($validated['email']));

        $user = User::whereRaw('LOWER(email) = ?', [$validated['email']])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            Log::error('Login failed: invalid credentials', [
                'email' => $validated['email'],
            ]);

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user->makeHidden(['password', 'remember_token']);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $email = strtolower(trim($request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $status = Password::sendResetLink(['email' => $email]);

            if ($status !== Password::RESET_LINK_SENT) {
                Log::error('Password reset link sending failed', [
                    'email' => $email,
                    'status' => $status,
                ]);

                return response()->json(['email' => __($status)], 400);
            }

            return response()->json(['message' => __($status)]);
        } catch (\Throwable $e) {
            Log::error('Password reset link sending exception', [
                'email' => $email,
                'exception' => $e,
            ]);

            return response()->json(['message' => 'Unable to send password reset link'], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $email = strtolower(trim($request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            });

            if ($status !== Password::PASSWORD_RESET) {
                Log::error('Password reset failed', [
                    'email' => $email,
                    'status' => $status,
                    'token' => $request->input('token'),
                ]);

                return response()->json(['message' => __($status)], 400);
            }

            return response()->json(['message' => __($status)]);
        } catch (\Throwable $e) {
            Log::error('Password reset exception', [
                'email' => $email,
                'token' => $request->input('token'),
                'exception' => $e,
            ]);

            return response()->json(['message' => 'Unable to reset password'], 500);
        }
    }

    public function socialLogin(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:google,facebook',
            'token' => 'required|string',
        ]);

        try {
            $provider = Socialite::driver($request->provider);

            if (! $provider instanceof \Laravel\Socialite\Two\AbstractProvider) {
                return response()->json(['message' => 'Unsupported provider'], 400);
            }

            $providerUser = $provider->userFromToken($request->token);

            $user = User::firstOrCreate(
                ['email' => $providerUser->getEmail()],
                [
                    'name' => $providerUser->getName(),
                    'provider' => $request->provider,
                    'provider_id' => $providerUser->getId(),
                    'role' => 'user'
                ]
            );

            if (! $user->provider) {
                $user->update([
                    'provider' => $request->provider,
                    'provider_id' => $providerUser->getId(),
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            Log::error('Social login failed: ' . $e->getMessage());

            return response()->json(['message' => 'Invalid token or provider'], 401);
        }
    }
}
