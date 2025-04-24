<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Dotenv\Exception\ValidationException;

class AuthController extends Controller
{
    use HttpResponses;

    /**
     * Login user with email and password
     */
    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken(
                $request->input('token_name', 'default'),
                ['*'],
                now()->addMinutes(5)
            )->plainTextToken;

            // Token de refresh (longa duração)
            $refreshToken = $user->createToken(
                $request->input('token_name', 'refresh'),
                ['*'],
                now()->addDays(7)
            )->plainTextToken;

            return $this->success([
                'user' => $user,
                'token' => $token,
                'refresh_token' => $refreshToken,
            ], "Authenticated");
        }

        return $this->error('Invalid credentials', [], 401);
    }

    /**
     * Register user
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $user = new User;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->save();

            return $this->success([
                'user' => $user,
            ], "Registered");
        } catch (ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (Exception $e) {
            return $this->error('Validation failed', $e->errors(), 500);
        }
    }

    /**
     * Refresh a token
     */
    public function refresh(Request $request)
    {
        try{
            $refreshToken = $request->bearerToken();

            if (!$refreshToken) {
                return $this->error('No refresh token provided', [], 401);
            }

            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($refreshToken);

            if (
                !$tokenModel ||
                !$tokenModel->can('*') ||
                $tokenModel->expires_at->isPast()
            ) {
                return $this->error('Invalid or expired refresh token', [], 401);
            }

            $user = $tokenModel->tokenable;

            $tokenModel->delete();

            $newAccessToken = $user->createToken('access', ['*'], now()->addMinutes(5))->plainTextToken;
            $newRefreshToken = $user->createToken('refresh', ['*'], now()->addDays(7))->plainTextToken;

            return $this->success([
                'user' => $user,
                'token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
            ], 'Token refreshed');
        } catch (\Throwable $e) {
            return $this->error(
                'Something went wrong',
                ['exception' => $e->getMessage()],
                500
            );
        }
    }
}
