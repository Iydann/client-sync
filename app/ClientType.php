<?php

namespace App;

enum ClientType: string
{
    case Individual = 'individual';
    case Organization = 'organization';

    public function getLabel(): string
    {
        return match($this) {
            self::Individual => 'Individual',
            self::Organization => 'Organization',
        };
    }
}
