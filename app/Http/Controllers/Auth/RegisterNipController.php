<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RegisterNipController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|exists:employees,nip'
        ], [
            'nip.required' => 'NIP wajib diisi',
            'nip.exists' => 'NIP tidak ditemukan di data pegawai',
        ]);

        $employee = Employee::where('nip', $request->nip)->first();

        // Cek apakah sudah punya user
        if ($employee->id_user && $employee->user->email_verified_at) {
            return back()->withErrors(['nip' => 'NIP ini sudah terdaftar sebagai user.']);
        }

        // Buat user baru
        DB::beginTransaction();
        try {

            $user = User::where('email', $employee->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $employee->nama_pegawai,
                    'email' => $employee->email,
                    'password' => '', // Akan diatur setelah verifikasi email
                ]);

                // Assign role 'user'
                $user->assignRole(Role::firstOrCreate(['name' => 'user']));

                // Update employee
                $employee->id_user = $user->id;
                $employee->save();
            }

            // Kirim email verifikasi (link set password, expired 1 hari)
            // Gunakan tabel password_resets agar token selalu satu per email
            $token = \Str::random(64);
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => \Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $url = URL::temporarySignedRoute(
                'set-password.form',
                now()->addDay(),
                ['token' => $token, 'email' => $user->email]
            );

            \Mail::send('emails.set-password', [
                'user' => $user,
                'url' => $url,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Verifikasi Email & Atur Password Akun Anda');
            });

            DB::commit();
            return redirect()->route('register-nip.sent');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['nip' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function sent()
    {
        return view('auth.register-nip-sent');
    }
}
