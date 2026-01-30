<?php

namespace App\Filament\Traits;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker; // Import ini
use Illuminate\Support\Facades\DB;            // Import ini
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

        // KASUS 1: USER MASIH INVITED (Kirim Link Setup Password Custom)
        if ($user->status === 'invited' && $user->invitation_token) {
            try {
                Mail::to($user->email)
                    ->send(new ClientInvitationMail($user, $user->invitation_token, 'invite'));
                
                Notification::make()->title('Invitation Sent.')->success()->send();
            } catch (\Exception $e) {
                Notification::make()->title('Failed to Send Invitation.')->body($e->getMessage())->warning()->send();
            }
        }
        
        // KASUS 2: USER SUDAH READY (Kirim Link Reset Password Resmi)
        elseif ($user->status === 'ready') {
            try {
                // 1. Bersihkan token lama di tabel password_reset_tokens
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();

                // 2. Buat Token Resmi via Broker
                /** @var PasswordBroker $broker */
                $broker = Password::broker();
                $token = $broker->createToken($user);

                // 3. Kirim Email Tipe 'reset' (Link menuju halaman Reset Password Bawaan)
                Mail::to($user->email)
                    ->send(new ClientInvitationMail($user, $token, 'reset'));

                Notification::make()
                    ->title('Link Reset Password Terkirim')
                    ->body("Email reset password dikirim ke {$user->email}")
                    ->success()
                    ->send();

            } catch (\Exception $e) {
                Notification::make()->title('Gagal Email')->body($e->getMessage())->warning()->send();
            }
        }
    }
}