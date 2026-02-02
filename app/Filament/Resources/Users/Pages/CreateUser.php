<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasInvitationLogic; // 1. Import Trait

class CreateUser extends CreateRecord
{
    use HasInvitationLogic; // 2. Pasang Trait

    protected static string $resource = UserResource::class;

    protected bool $shouldSendInvitation = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ambil status toggle invite
        $isInviting = $data['send_invitation'] ?? false;
        
        // Simpan status untuk digunakan di afterCreate
        $this->shouldSendInvitation = $isInviting;
        
        // Hapus field toggle dari data agar tidak error SQL
        unset($data['send_invitation']);

        // Panggil fungsi dari Trait untuk memproses data
        // Fungsi ini akan otomatis mengisi password dummy jika $isInviting = true
        return $this->prepareUserData($data, $isInviting);
    }

    protected function afterCreate(): void
    {
        // HANYA kirim email jika toggle aktif
        if ($this->shouldSendInvitation) {
            $this->sendInvitationEmail($this->record);
        }
    }
}