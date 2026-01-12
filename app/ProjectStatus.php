<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ProjectStatus: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string|Htmlable|null
    {
        return match($this) {
            ProjectStatus::Pending => 'Pending',
            ProjectStatus::InProgress => 'In Progress',
            ProjectStatus::Completed => 'Completed',
            ProjectStatus::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            ProjectStatus::Pending => 'gray',
            ProjectStatus::InProgress => 'info',
            ProjectStatus::Completed => 'success',
            ProjectStatus::Cancelled => 'danger',
        };
    }
}
