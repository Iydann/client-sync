<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{
    public function show($token)
    {
        // Cari user by token
        $user = User::where('invitation_token', $token)->first();

        if (!$user) {
            abort(404, 'Link undangan tidak valid atau sudah kadaluarsa.');
        }

        return view('auth.client-setup-password', compact('user', 'token'));
    }

    public function store(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('invitation_token', $token)->firstOrFail();

        // Update User
        $user->update([
            'password' => Hash::make($request->password),
            'status' => 'active',
            'invitation_token' => null, // Hapus token biar link mati (single use)
            'email_verified_at' => now(),
        ]);

        // Auto Login
        Auth::login($user);

        // Redirect ke dashboard (sesuaikan dengan path dashboard filament/project)
        return redirect('/portal'); 
    }
}