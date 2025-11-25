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
    public function showForm(){
        return view('registers.register');
    }

    public function postRegister(RegisterRequest $request){
        // RegisterRequest already validates the input

        User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'phone' => $request->get('phone'),
            'address' => $request->get('address'),
        ]);

        return redirect()->route('showLogin')->with('message', 'Đăng ký thành công!');
    }

    public function showLogin(){
        return view('registers.login');
    }

    public function postLogin(LoginRequest $request){
        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            $user = Auth::user();
            if($user->role === 'admin'){
                return redirect()-> route('showManager')->with('message', 'Đăng nhập thành công!');
            } else if($user->role === 'customer'){
                return redirect()-> route('home')->with('message', 'Đăng nhập thành công!');
            }
            // Default fallback
            return redirect()->route('home')->with('message', 'Đăng nhập thành công!');
        }

        // Failed authentication
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.'
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('showLogin');
    }

    
}