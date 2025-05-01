<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'nip' => 'required',
                'password' => 'required|min:6'
            ]);

            $user = User::where('nip', $request->nip)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new Exception('Unauthorized, nip or password is wrong!', 401);
            }

            if ($user->is_active == 0) {
                throw new Exception('Unauthorized, user is not active!', 401);
            }

            if ($user->deleted == 1) {
                throw new Exception('Unauthorized, user is deleted!', 401);
            }

            $expires = now()->addDay();
            $token = $user->createToken('access_token', ['*'], $expires)->plainTextToken;
            $token = explode('|', $token)[1];
            $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(7))->plainTextToken;
            $refreshToken = explode('|', $refreshToken)[1];

            return response()->json([
                'status' => true,
                'message' => 'Login successfull.',
                'data' => [
                    'token' => $token,
                    'expires_in' => $expires->toDateTimeString(),
                    'refresh_token' => $refreshToken
                ]
            ]);
        } catch (\Throwable $e) {
            $code = 500;
            if ($e->getCode()) {
                $code = $e->getCode();
            }
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $request->only(['refresh_token']);

            $user = User::whereHas('tokens', function ($query) use ($request) {
                $query->where('token', hash('sha256', $request->refresh_token))
                    ->where('name', 'refresh_token');
            })->first();

            if (!$user) {
                throw new Exception('Invalid refresh token.', 401);
            }

            $expires = now()->addDay();

            $newToken = $user->createToken('access_token', ['*'], $expires)->plainTextToken;
            $newToken = explode('|', $newToken)[1];

            return response()->json([
                'status' => true,
                'message' => 'Successfull.',
                'data' => [
                    'token' => $newToken,
                    'expires_in' => $expires->toDateTimeString()
                ]
            ]);
        } catch (\Throwable $e) {
            $code = 500;
            if ($e->getCode()) {
                $code = $e->getCode();
            }
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function logout(Request $request)
    {
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (!$accessToken) {
            throw new Exception('Invalid access token.', 401);
        }

        $accessToken->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
