<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\ApiFormatter;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'logout']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
	    $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only(['email','password']);

        if (! $token = Auth::attempt($credentials)) {
            return ApiFormatter::sendResponse (400,'user not found', 'silahkan cek kembali email dan password anda');
        }

        $respondWithToken = [
            'acces_token' => $token,
            'token_type' => 'bearer',
            'user' =>auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ];
    
    return ApiFormatter::sendResponse(200,'logged-in', $respondWithToken);
    }

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return ApiFormatter::sendResponse(200,'succes', auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return ApiFormatter::sendResponse(200,'succes' , 'berhasil logout');
    }

    public function _construct()
{
    $this->middleware('auth:api');
}
    
}