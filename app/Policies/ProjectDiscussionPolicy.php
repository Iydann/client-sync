<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProjectDiscussion;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectDiscussionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectDiscussion');
    }

    public function view(AuthUser $authUser, ProjectDiscussion $projectDiscussion): bool
    {
        return $authUser->can('View:ProjectDiscussion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectDiscussion');
    }

    public function update(AuthUser $authUser, ProjectDiscussion $projectDiscussion): bool
    {
        return $authUser->can('Update:ProjectDiscussion');
    }

    public function delete(AuthUser $authUser, ProjectDiscussion $projectDiscussion): bool
    {
        return $authUser->can('Delete:ProjectDiscussion');
    }

    public function restore(AuthUser $authUser, ProjectDiscussion $projectDiscussion): bool
    {
        return $authUser->can('Restore:ProjectDiscussion');
    }

    public function forceDelete(AuthUser $authUser, ProjectDiscussion $projectDiscussion): bool
    {
        return $authUser->can('ForceDelete:ProjectDiscussion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectDiscussion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectDiscussion');
    }

    public function replicate(AuthUser $authUser, ProjectDiscussion $projectDiscussion): bool
    {
        return $authUser->can('Replicate:ProjectDiscussion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectDiscussion');
    }

}