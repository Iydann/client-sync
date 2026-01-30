<!DOCTYPE html>
<html>
<head>
    <title>{{ $type === 'reset' ? 'Reset Password' : 'Undangan Akun' }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    
    <h2>Halo, {{ $user->name }}</h2>

    @if($type === 'reset')
        {{-- TAMPILAN KHUSUS RESET PASSWORD --}}
        <p>Kami menerima permintaan untuk mengatur ulang password akun Anda.</p>
        <p>Silakan klik tombol di bawah ini untuk membuat password baru:</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </p>
        
        <p>Link ini akan kadaluarsa dalam 60 menit.</p>
        <p>Jika Anda tidak meminta reset password, abaikan saja email ini.</p>

    @else
        {{-- TAMPILAN KHUSUS UNDANGAN --}}
        <p>Akun Anda telah dibuat di portal <strong>Client Sync</strong>.</p>
        <p>Silakan klik tombol di bawah ini untuk mengaktifkan akun dan membuat password Anda:</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Aktivasi Akun & Buat Password
            </a>
        </p>
    @endif

    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="font-size: 12px; color: #777;">
        Jika tombol di atas tidak berfungsi, salin link ini: <br> 
        <a href="{{ $url }}">{{ $url }}</a>
    </p>
</body>
</html>