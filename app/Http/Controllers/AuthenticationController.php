<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function signUp(SignUpRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'user' => new UserResource($user),
            'accessToken' => $token
        ], 201);
    }

    public function signIn(SignInRequest $request)
    {
        $fields = $request->validated();
        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'user' => new UserResource($user),
            'accessToken' => $token
        ], 200);
    }
}
