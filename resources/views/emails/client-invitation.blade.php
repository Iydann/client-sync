<!DOCTYPE html>
<html>
<head>
    <title>{{ $type === 'reset' ? 'Reset Password' : 'Account Invitation' }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    
    <h2>Hello, {{ $user->name }}</h2>

    @if($type === 'reset')
        {{-- RESET PASSWORD VIEW --}}
        <p>We received a request to reset your account password.</p>
        <p>Please click the button below to create a new password:</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </p>
        
        <p>This link will expire in 60 minutes.</p>
        <p>If you did not request a password reset, please ignore this email.</p>

    @else
        {{-- INVITATION VIEW --}}
        <p>Your account has been created on the <strong>Client Sync</strong> portal.</p>
        <p>Please click the button below to activate your account and set your password:</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Activate Account & Set Password
            </a>
        </p>
    @endif

    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="font-size: 12px; color: #777;">
        If the button above does not work, copy and paste this link into your browser: <br> 
        <a href="{{ $url }}">{{ $url }}</a>
    </p>
</body>
</html>