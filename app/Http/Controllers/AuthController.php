<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Agrega esta línea para importar la clase Auth
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response; // Agrega esta línea para importar la clase Response

class AuthController extends Controller
{
    public function user()
    {
        return Auth::user();
    }

    public function register(Request $request)
    {
        $role = ucwords(strtolower($request->input('cargo_institucional')));
        $user = User::create([
            'name' => $request->input('name'),
            'apellido' => $request->input('apellido'),
            'email' => $request->input('email'),
            'telefono' => $request->input('telefono'),
            'cargo_institucional' => $role,
            'password' => Hash::make($request->input('password'))
        ]);

        $user->assignRole($role);

        $token = $user->createToken('auth_token')->accessToken;
        return response([
            'token' => $token,
            'role' => $user->getRoleNames()
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;
        // $token = $user->createToken('auth_token')->accessToken;

        $cookie = cookie('cookie_token', $token, 60 * 24);

        return response([
            'message' => 'Success',
            'token' => $token,
            'user' => $user->cargo_institucional,
            'roles' => $user->getRoleNames()
        ])->withoutCookie($cookie);
    }

    public function logout(Request $request)
    {
        $cookie = Cookie::forget('jwt');
        // $request->user()->token()->revoke();

        return response([
            'message' => 'Success'
        ])->withCookie($cookie);
    }

    public function AllUsers()
    {
        return User::all();
    }
}
