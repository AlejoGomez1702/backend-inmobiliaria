<?php

namespace App\Http\Controllers\Api;

use App\Providers\HttpRequestsProvider as ClientHttp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Inicio de sesión y creación de token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        $user_id = $this->getUserId($request->email);

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user_email' => $request->email,
            'user_id'    => $user_id,
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
        ]);
    }

    /**
     * Cierre de sesión (anular el token)
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Obtener el objeto User como json
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Obtiene el Id del usuario legueado en WASI
     */
    public function getUserId($user_email)
    {
        $client = new ClientHttp('');
        $users = (array) $client->get('user/all-users');

        $user_selected;
        $selected = false;
        foreach ($users as $user) 
        {
            if($user['email'] == $user_email)
            {
                $user_selected = $user;
                $selected = true;
                break;
            }
        }

        if($selected)
        {
            return $user_selected['id_user'];
        }

        return null;
    }
}
