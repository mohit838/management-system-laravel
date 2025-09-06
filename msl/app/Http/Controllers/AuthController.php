<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Login and return access + refresh token.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $accessToken = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        // Create new refresh token
        $refreshToken = RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        return $this->respondWithToken($accessToken, $refreshToken->token);
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * Logout (invalidate the access token + revoke refresh token).
     */
    public function logout(Request $request)
    {
        // Invalidate JWT access token
        Auth::logout();

        // Optionally revoke the provided refresh token
        if ($request->has('refresh_token')) {
            RefreshToken::where('token', $request->input('refresh_token'))
                ->update(['revoked' => true]);
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh the token (issue a new access + refresh token).
     */
    public function refresh(Request $request)
    {
        $oldRefreshToken = RefreshToken::where('token', $request->input('refresh_token'))
            ->where('revoked', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $oldRefreshToken) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 401);
        }

        $user = $oldRefreshToken->user;
        $newAccessToken = Auth::login($user);

        // Revoke old refresh token
        $oldRefreshToken->update(['revoked' => true]);

        // Issue new refresh token
        $newRefreshToken = RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        return $this->respondWithToken($newAccessToken, $newRefreshToken->token);
    }

    /**
     * Helper: format the token response.
     */
    protected function respondWithToken($accessToken, $refreshToken = null)
    {
        return response()->json([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'bearer',
            'expires_in'    => Auth::factory()->getTTL() * 60, // seconds
        ]);
    }
}
