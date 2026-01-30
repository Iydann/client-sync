<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Filament\Traits\HasInvitationLogic; // 1. Import Trait

class CreateClient extends CreateRecord
{
    use HasInvitationLogic; // 2. Pakai Trait

    protected static string $resource = ClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create($data['user']);
        $user->assignRole('client');
        
        return static::getModel()::create([
            'user_id' => $user->id,
            'client_name' => $data['client_name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $isInviting = $data['send_invitation'] ?? false;
        $userData = $data['user'];
        unset($data['send_invitation']);

        // 3. Gunakan fungsi dari Trait
        $data['user'] = $this->prepareUserData($userData, $isInviting);

        return $data;
    }

    protected function afterCreate(): void
    {
        $client = $this->record;
        // 4. Gunakan fungsi kirim email dari Trait
        $this->sendInvitationEmail($client->user);
    }
}