<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; padding: 20px;">
    <h2>Reset Password</h2>
    <p>Kami menerima permintaan reset password untuk akun kamu.</p>
    <p>Klik tombol berikut untuk reset password. Link berlaku <strong>60 menit</strong>.</p>
    <a href="{{ $resetUrl }}"
       style="display:inline-block; padding:12px 24px; background:#2563eb; color:#fff; border-radius:6px; text-decoration:none;">
        Reset Password
    </a>
    <p style="margin-top:20px; color:#6b7280; font-size:13px;">
        Jika kamu tidak merasa melakukan permintaan ini, abaikan email ini.
    </p>
</body>
</html>