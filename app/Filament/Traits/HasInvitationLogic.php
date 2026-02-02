<?php

namespace App\Filament\Traits;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\DB;
use App\Mail\ClientInvitationMail;
use Filament\Notifications\Notification;
use App\Models\User;

trait HasInvitationLogic
{
    public function prepareUserData(array $userData, bool $isInviting): array
    {
        if ($isInviting) {
             $userData['password'] = Hash::make(Str::random(32));
             $userData['status'] = 'invited';
             $userData['invitation_token'] = Str::random(64); 
        } else {
             if (!empty($userData['password'])) {
                 $userData['password'] = Hash::make($userData['password']);
             }
             $userData['status'] = 'ready';
             $userData['invitation_token'] = null;
        }
        return $userData;
    }

    public function sendInvitationEmail(?User $user): void
    {
        if (!$user) return;

        if ($user->status === 'invited' && $user->invitation_token) {
            try {
                Mail::to($user->email)
                    ->send(new ClientInvitationMail($user, $user->invitation_token, 'invite'));
                
                Notification::make()
                    ->title('Invitation Link Sent')->success()->send()
                    ->body("An invitation email has been sent to {$user->email}")
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()->title('Failed to Send Invitation.')->body($e->getMessage())->warning()->send();
            }
        }

        elseif ($user->status === 'ready') {
            try {
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();

                /** @var PasswordBroker $broker */
                $broker = Password::broker();
                $token = $broker->createToken($user);

                Mail::to($user->email)
                    ->send(new ClientInvitationMail($user, $token, 'reset'));

                Notification::make()
                    ->title('Password Reset Link Sent')
                    ->body("A password reset email has been sent to {$user->email}")
                    ->success()
                    ->send();

            } catch (\Exception $e) {
                Notification::make()->title('Gagal Email')->body($e->getMessage())->warning()->send();
            }
        }
    }
}