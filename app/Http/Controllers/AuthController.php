<?php

namespace App\Http\Controllers;

use App\User;
use App\Shift;
use App\Log;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        if(!$user->shift()->count() > 0){
            $shift = new Shift([
                'start'   => date("Y-m-d H:i:s"),
                'user_id' => $user->id,
            ]);

            $user->shift()->save($shift);
        }


        return $this->respondWithToken($token);
    }

    public function register(Request $request, User $user)
    {
        $rules = $user::VALIDATION;

        $data  = $request->validate($rules);

        $data['usergroup_id'] = 2; //not admin for default

        try {
            $createdUser = $user->create($data);
            return response($createdUser, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = auth()->user();

        $shift = $user->shift()->first();

        $log = new Log([
            'start'   => $shift->start,
            'finish'  => date("Y-m-d H:i:s"),
            'user_id' => $user->id
        ]);

        $log->save();

        $shift->delete();

        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
