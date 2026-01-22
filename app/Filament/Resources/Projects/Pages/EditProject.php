<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserContribution;
use Illuminate\Support\Facades\Auth;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Catat kontribusi user (update project)
        $user = Auth::user();
        if ($user) {
            UserContribution::create([
                'user_id' => $user->id,
                'type' => 'update_project',
                'value' => 1,
                'year' => now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $data;
    }

    protected function afterDelete(): void
    {
        // Catat kontribusi user (delete project)
        $user = Auth::user();
        if ($user) {
            UserContribution::create([
                'user_id' => $user->id,
                'type' => 'delete_project',
                'value' => 1,
                'year' => now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
