<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Milestone;
use Illuminate\Auth\Access\HandlesAuthorization;

class MilestonePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Milestone');
    }

    public function view(AuthUser $authUser, Milestone $milestone): bool
    {
        return $authUser->can('View:Milestone');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Milestone');
    }

    public function update(AuthUser $authUser, Milestone $milestone): bool
    {
        return $authUser->can('Update:Milestone');
    }

    public function delete(AuthUser $authUser, Milestone $milestone): bool
    {
        return $authUser->can('Delete:Milestone');
    }

    public function restore(AuthUser $authUser, Milestone $milestone): bool
    {
        return $authUser->can('Restore:Milestone');
    }

    public function forceDelete(AuthUser $authUser, Milestone $milestone): bool
    {
        return $authUser->can('ForceDelete:Milestone');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Milestone');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Milestone');
    }

    public function replicate(AuthUser $authUser, Milestone $milestone): bool
    {
        return $authUser->can('Replicate:Milestone');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Milestone');
    }

}