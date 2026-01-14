<?php

namespace App;

enum ClientType: string
{
    case Individual = 'individual';
    case PT = 'pt';
    case CV = 'cv';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match($this) {
            self::Individual => 'Individual',
            self::PT => 'PT',
            self::CV => 'CV',
            self::OTHER => 'Other',
        };
    }
}
