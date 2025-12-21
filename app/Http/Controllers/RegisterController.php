<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{

    public function postRegister(RegisterRequest $request){
        // RegisterRequest already validates the input
        $data = $request->validated();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function postLogin(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        // Không tồn tại user
        if (!$user) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng.'], 401);
        }

        // DB của bạn đang cho password NULL -> chặn luôn
        if (empty($user->password)) {
            return response()->json(['message' => 'Tài khoản chưa có mật khẩu.'], 401);
        }

        // Check password hash
        if (!Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng.'], 401);
        }

        // (tuỳ chọn) Nếu muốn mỗi lần login chỉ giữ 1 token
        // $user->tokens()->delete();

        $token = $user->createToken('vue-spa')->plainTextToken;

        // Ẩn password trước khi trả về
        $user->makeHidden(['password']);

        return response()->json([
        'status' => 'success',
        'token' => $token,
        'user'  => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            ]
        ]);
    }


    public function logout(Request $request)
    {
        // Cần gửi Authorization: Bearer <token>
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ], 200);
    }

}
