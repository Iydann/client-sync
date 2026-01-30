<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\ClientInvitationMail;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // Variabel penampung status toggle
    protected bool $shouldSendInvite = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Cek apakah toggle dicentang sebelum disimpan
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Cek input 'send_invitation' dari form
        if (isset($data['send_invitation']) && $data['send_invitation'] === true) {
            $this->shouldSendInvite = true;
            
            // Opsional: Reset password jadi acak supaya password lama tidak berlaku
            // $data['password'] = Hash::make(Str::random(32));
        }

        // Hapus field 'send_invitation' agar tidak error SQL (karena kolom ini tidak ada di tabel users)
        unset($data['send_invitation']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Hanya jalankan jika toggle tadi dicentang
        if ($this->shouldSendInvite) {
            $user = $this->record; // Ambil data user yang sedang diedit

            // Generate token baru
            $newToken = Str::random(64);
            
            // Update user: set token baru & ubah status jadi pending lagi
            $user->update([
                'invitation_token' => $newToken,
                'status' => 'pending', 
            ]);

            // Kirim Email via Brevo
            try {
                Mail::to($user->email)->send(new ClientInvitationMail($user, $newToken, 'reset'));
    
                Notification::make()
                    ->title('Email Reset Terkirim')
                    ->body("Link reset password telah dikirim ke {$user->email}")
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Gagal Mengirim Email')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }
}