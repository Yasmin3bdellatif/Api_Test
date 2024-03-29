<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password'=> bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myAppToken')
            ->plainTextToken;

        $response = [
            'user' =>$user,
            'token' =>$token
        ];

        return response($response, 201);

    }

    public function logout(Request $request)
    {
        auth()->user()->token()->delete();

        return [
            'message' => 'LoggedOut'
        ];
    }

    public function login(Request $request)
    {
        // Validate the request data
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        //check email
        $user = User::where('email', $fields['email'])
            ->first();

        //check password
        if(!$user || !Hash::check($fields['password'], $user->password))
        {
            return response([
                'message' => 'Bad creds'] , 401);
        }

        $token = $user->createToken('myAppToken')
            ->plainTextToken;

        $response = [
            'user' =>$user,
            'token' =>$token
        ];

        return response($response, 201);

    }
}
