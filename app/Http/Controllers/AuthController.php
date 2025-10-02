<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:3',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard')->with('success', 'Login successful');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }


    public function EditPassword()
    {

        return view('admin.updatepassword');
    }


    public function updatepassword(Request $request)
    {

        $user = Auth::user();


        $user->email = $request->email;
        $user->password = Hash::make($request->password);


        $user->save();
        return redirect()->route('login')->with('success', 'Password updated successfully');
    }
}
