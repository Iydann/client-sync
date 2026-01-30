<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Filament\Facades\Filament; // WAJIB ADA INI

class ClientInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    public $type; // Untuk menyimpan tipe: 'invite' atau 'reset'

    // Perhatikan: Ada parameter ke-3 '$type' dengan default 'invite'
    public function __construct(User $user, $token, $type = 'invite')
    {
        $this->user = $user;
        $this->type = $type;

        if ($type === 'reset') {
            // JIKA RESET PASSWORD (DARI FORGOT PASSWORD)
            // Gunakan helper Filament untuk link reset yang valid
            $this->url = Filament::getResetPasswordUrl($token, $user);
        } else {
            // JIKA UNDANGAN BARU
            // Gunakan route custom kita
            $this->url = route('invitation.show', ['token' => $token]);
        }
    }

    public function envelope(): Envelope
    {
        // Subject berubah sesuai tipe
        $subject = $this->type === 'reset' 
            ? 'Reset Password Request - Client Sync' 
            : 'Welcome to Client Sync - Please Set Password';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}