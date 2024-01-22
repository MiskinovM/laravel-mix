<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function create() {
        return view('user.create');
    }

    public function store(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validated->fails()) {
            return redirect()->route('register.create')->withErrors($validated)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        session()->flash('success', 'Регистрация пройдена');
        Auth::login($user);
        return redirect()->route('home');
    }

    public function loginForm(){
        return view('user.login');
    }

    public function login(Request $request) {

        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validated->fails()) {
            return redirect()->route('login.create')->withErrors($validated)->withInput();
        }

        // Пробуем авторизовать пользователя
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            session()->flash('success', 'You are logged');

            // Если пользователь админ то перенаправляем его на админ панель
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.index');
            }
            else {
                return redirect()->route('home');
            }
        }

        return redirect()->route('login.create')->with('error', 'Incorrect login or password');
    }

    public function logout() {
        Auth::logout();

        return redirect()->route('login.create');
    }
}
