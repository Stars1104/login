<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Wrong Email Type');
                    }
                }
            ],
            'password' => 'required|string|min:8|max:16|confirmed',
            'fullName' => 'required|string|max:255',
            'userName' => 'required|string|max:10|unique:users',
            'companyName' => 'required|string|max:255',
            'phoneNumber' => ['required', 'regex:/^\\+?[0-9]{7,15}$/'],
            'role' => 'required|string|in:admin,user',
            'comments' => 'nullable|string',
            'password_confirmation' => 'required|string|min:8|max:16',
        ], [
            'email.unique' => 'User Already Registered!',
            'password.confirmed' => 'Check your password',
            'userName.max' => 'Username length must be at most 10 characters',
            'password.min' => 'Password must be at least 8 characters',
            'password.max' => 'Password must be less than 16 characters',
            'phoneNumber.regex' => 'Invalid phone number format',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('email') && $errors->first('email') === 'User Already Registered!') {
                return response()->json(['message' => 'User Already Registered!'], 409);
            }
            if ($errors->has('password') && $errors->first('password') === 'Check your password') {
                return response()->json(['message' => 'Check your password'], 422);
            }
            if ($errors->has('email') && $errors->first('email') === 'Wrong Email Type') {
                return response()->json(['message' => 'Wrong Email Type'], 422);
            }
            return response()->json($errors, 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'fullName' => $request->fullName,
            'userName' => $request->userName,
            'companyName' => $request->companyName,
            'phoneNumber' => $request->phoneNumber,
            'role' => $request->role,
            'comments' => $request->comments
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Wrong Email Type');
                    }
                }
            ],
            'password' => 'required|string|min:8|max:16',
        ], [
            'email.email' => 'Wrong Email Type',
            'password.min' => 'Password must be at least 8 characters',
            'password.max' => 'Password must be less than 16 characters',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('email') && $errors->first('email') === 'Wrong Email Type') {
                return response()->json(['message' => 'Wrong Email Type'], 422);
            }
            return response()->json($errors, 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]
        ]);
    }

    public function getUser()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user()
        ]);
    }

    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'fullName' => 'sometimes|string|max:255',
            'userName' => 'sometimes|string|max:255|unique:users,userName,' . $user->id,
            'companyName' => 'sometimes|string|max:255',
            'phoneNumber' => 'sometimes|string|max:20',
            'role' => 'sometimes|string|in:admin,user',
            'comments' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only([
            'email',
            'fullName',
            'userName',
            'companyName',
            'phoneNumber',
            'role',
            'comments'
        ]);

        User::where('id', $user->id)->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]
        ]);
    }
}
