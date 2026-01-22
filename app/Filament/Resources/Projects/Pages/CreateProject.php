<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserContribution;
use Illuminate\Support\Facades\Auth;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $project = parent::handleRecordCreation($data);

        // Catat kontribusi user (misal: user yang login membuat project)
        $user = Auth::user();
        if ($user) {
            UserContribution::create([
                'user_id' => $user->id,
                'type' => 'create_project',
                'value' => 1,
                'year' => now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $project;
    }
}
