<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use App\Mail\ClientInvitationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create user with client role
        $user = User::create($data['user']);
        $user->assignRole('client');
        
        // Create client with user_id
        return static::getModel()::create([
            'user_id' => $user->id,
            'client_name' => $data['client_name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cek status toggle
        $isInviting = $data['send_invitation'] ?? false;

        // Ambil data user dari form array 'user'
        $userData = $data['user'];

        if ($isInviting) {
            // Jika kirim undangan:
            // 1. Buat password random (dummy) karena kolom password wajib di DB
            $userData['password'] = Hash::make(Str::random(32));
            // 2. Set status pending
            $userData['status'] = 'pending';
            // 3. Generate token
            $userData['invitation_token'] = Str::random(64);
        } else {
            // Jika set password manual:
            // 1. Hash password inputan admin
            $userData['password'] = Hash::make($userData['password']);
            // 2. Langsung aktif
            $userData['status'] = 'active';
            $userData['invitation_token'] = null;
        }

        // Kembalikan data user yang sudah dimodifikasi ke array utama
        $data['user'] = $userData;
        
        // Hapus field 'send_invitation' agar tidak error (karena kolom ini ga ada di tabel clients)
        unset($data['send_invitation']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Ambil record Client yg baru dibuat
        $client = $this->record;
        
        // Ambil User relasinya (pastikan di Model Client ada public function user())
        $user = $client->user; 

        // Cek jika status pending & ada token, kirim email
        if ($user && $user->status === 'pending' && $user->invitation_token) {
            try {
                // Kirim via Brevo SMTP
                Mail::to($user->email)->send(new ClientInvitationMail($user, $user->invitation_token));
                
                Notification::make()
                    ->title('Undangan Terkirim')
                    ->body("Email verifikasi berhasil dikirim ke {$user->email}")
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Gagal Mengirim Email')
                    ->body('Data tersimpan, tapi email gagal terkirim: ' . $e->getMessage())
                    ->warning()
                    ->send();
            }
        }
    }
}
