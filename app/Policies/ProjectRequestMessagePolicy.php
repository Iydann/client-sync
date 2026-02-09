<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProjectRequestMessage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProjectRequestMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectRequestMessage');
    }

    public function view(AuthUser $authUser, ProjectRequestMessage $message): bool
    {
        return $authUser->can('View:ProjectRequestMessage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectRequestMessage');
    }
}
