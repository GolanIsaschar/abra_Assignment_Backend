<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function userRegister(Request $request)
    {
        try {
            $userFields = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|max:20'
            ]);

            $userFields['password'] = bcrypt($userFields['password']);

            $user = User::create($userFields);
            if ($user) {
                auth()->login($user);
                return response()->json(['success' => 'You registered successfully', 'user' => $user], 201);
            }
        } catch (ValidationException $exception) {
            if (isset($exception->errors()['email']) && $exception->errors()['email'][0] === 'The email has already been taken.') {
                return response()->json(['error' => 'Email address is already in use.'], 422);
            }
            return response()->json(['error' => 'User creation failed. Please check your input.'], 422);
        }
    }

    public function userLogin(Request $request)
    {
        $loginFields = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (auth()->attempt(['email' => $loginFields['email'], 'password' => $loginFields['password']])) {
            $request->session()->regenerate();
            $user = auth()->user();
            return response()->json(['success' => 'You login successfully', 'user' => $user]);
        }

        $user = User::where('email', $loginFields['email'])->first();
        if ($user)
            return response()->json(['error' => 'Incorrect password. Please try again.'], 401);

        return response()->json(['error' => 'Email not found. Please check your email or register.'], 401);
    }

    public function userLogout()
    {
        if (auth()->check()) {
            auth()->logout();
            return response()->json(['success' => 'You logout successfully']);
        }

        return response()->json(['error' => 'You are not logged in.'], 401);
    }
}
