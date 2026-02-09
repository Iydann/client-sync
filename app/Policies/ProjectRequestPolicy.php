<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProjectRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectRequest');
    }

    public function view(AuthUser $authUser, ProjectRequest $projectRequest): bool
    {
        return $authUser->can('View:ProjectRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectRequest');
    }

    public function update(AuthUser $authUser, ProjectRequest $projectRequest): bool
    {
        return $authUser->can('Update:ProjectRequest');
    }

    public function delete(AuthUser $authUser, ProjectRequest $projectRequest): bool
    {
        return $authUser->can('Delete:ProjectRequest');
    }

    public function restore(AuthUser $authUser, ProjectRequest $projectRequest): bool
    {
        return $authUser->can('Restore:ProjectRequest');
    }

    public function forceDelete(AuthUser $authUser, ProjectRequest $projectRequest): bool
    {
        return $authUser->can('ForceDelete:ProjectRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectRequest');
    }

    public function replicate(AuthUser $authUser, ProjectRequest $projectRequest): bool
    {
        return $authUser->can('Replicate:ProjectRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectRequest');
    }

}