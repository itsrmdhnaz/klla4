{{-- Use plain HTML for email if mail markdown is not configured --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email & Atur Password</title>
</head>
<body>
    <div style="text-align:center;margin-bottom:24px;">
        <img src="{{ asset('images/logo_klla.svg') }}" alt="Background" style="max-width:300px;width:100%;height:auto;border-radius:12px;">
    </div>
    <h2>Verifikasi Email & Atur Password</h2>
    <p>Halo {{ $user->name }},</p>
    <p>
        Anda telah melakukan pendaftaran akun. Silakan klik tombol di bawah ini untuk mengatur password akun Anda. Link ini hanya berlaku selama 1 hari.
    </p>
    <p>
        <a href="{{ $url }}" style="display:inline-block;padding:10px 20px;background:#2563eb;color:#fff;text-decoration:none;border-radius:5px;">Atur Password</a>
    </p>
    <p>
        Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.
    </p>
    <p>
        Terima kasih,<br>
        {{ config('app.name') }}
    </p>
</body>
</html>
