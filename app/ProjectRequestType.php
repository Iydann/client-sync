<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum ProjectRequestType: string implements HasLabel
{
    case Bug = 'bug';
    case Enhancement = 'enhancement';
    case Question = 'question';
    case NewProject = 'new_project';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bug => 'Bug',
            self::Enhancement => 'Enhancement',
            self::Question => 'Question',
            self::NewProject => 'New Project',
        };
    }
}
