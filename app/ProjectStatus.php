<?php

namespace App;

enum ProjectStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            ProjectStatus::Pending => 'Pending',
            ProjectStatus::InProgress => 'In Progress',
            ProjectStatus::Completed => 'Completed',
            ProjectStatus::Cancelled => 'Cancelled',
        };
    }
}
