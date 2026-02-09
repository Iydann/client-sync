<?php

namespace App\Filament\Resources\ProjectRequests\Pages;

use App\Filament\Resources\ProjectRequests\ProjectRequestResource;
use App\Models\Project;
use App\ProjectRequestStatus;
use App\ProjectRequestType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateProjectRequest extends CreateRecord
{
    protected static string $resource = ProjectRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $data['created_by'] = $user?->id;
        $data['status'] ??= ProjectRequestStatus::Pending->value;
        $data['last_message_at'] = now();

        if (!empty($data['project_id'])) {
            $project = Project::find($data['project_id']);
            $data['client_id'] = $project?->client_id;
        } elseif ($user?->client) {
            $data['client_id'] = $user->client->id;
        }

        if (($data['type'] ?? null) === ProjectRequestType::NewProject->value && empty($data['client_id'])) {
            throw ValidationException::withMessages([
                'client_id' => 'Client is required for new project requests.',
            ]);
        }

        return $data;
    }
}
