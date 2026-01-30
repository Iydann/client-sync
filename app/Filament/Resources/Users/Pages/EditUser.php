<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use App\Filament\Traits\HasInvitationLogic;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    use HasInvitationLogic;

    protected static string $resource = UserResource::class;

    protected bool $shouldSendInvite = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Cek apakah toggle dicentang
        if (isset($data['send_invitation']) && $data['send_invitation'] === true) {
            $this->shouldSendInvite = true;
            
            if ($this->record->status !== 'ready') {
                $data['status'] = 'invited';
                $data['invitation_token'] = Str::random(64);
            }
        }

        unset($data['send_invitation']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->shouldSendInvite) {
            $this->sendInvitationEmail($this->record);
        }
    }
}