<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Filament\Facades\Filament;


class ClientInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    public $type;

    public function __construct(User $user, $token, $type = 'invite')
    {
        $this->user = $user;
        $this->type = $type;

        if ($type === 'reset') {
            $this->url = Filament::getResetPasswordUrl($token, $user, [], 'admin');
        } else {
            $this->url = route('invitation.show', ['token' => $token]);
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->type === 'reset' ? 'Set Your Password' : 'Welcome',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-invitation',
        );
    }
}