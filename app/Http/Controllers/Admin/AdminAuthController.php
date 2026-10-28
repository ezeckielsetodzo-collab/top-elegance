<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function create(Request $request)
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.orders.index');
        }

        return view('admin.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Indiquez votre adresse email.',
            'email.email' => 'L’adresse email est invalide.',
            'password.required' => 'Indiquez votre mot de passe.',
        ]);

        if (! Auth::attempt([...$credentials, 'is_admin' => true])) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.orders.index');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}