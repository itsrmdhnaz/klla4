<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetPasswordController extends Controller
{
    public function showForm(Request $request, $token)
    {
        $email = $request->query('email');
        // Cek token di tabel password_reset_tokens
        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (
            !$reset ||
            !Hash::check($token, $reset->token) ||
            now()->diffInHours($reset->created_at) > 24
        ) {
            return redirect()->route('login')->withErrors(['email' => 'Token tidak valid atau sudah kadaluwarsa.']);
        }

        return view('auth.set-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function setPassword(Request $request, $token)
    {
        $email = $request->input('email');
        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (
            !$reset ||
            !Hash::check($token, $reset->token) ||
            now()->diffInHours($reset->created_at) > 24
        ) {
            return redirect()->route('login')->withErrors(['email' => 'Token tidak valid atau sudah kadaluwarsa.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now();
        $user->save();

        // Hapus token setelah digunakan
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('status', 'Password berhasil diatur. Silakan login.');
    }
}
