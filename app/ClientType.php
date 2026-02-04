<?php

namespace App;

enum ClientType: string
{
    case Individual = 'individual';
    case Corporate = 'corporate';
    case Government = 'government';

    public function getLabel(): string
    {
        return match($this) {
            self::Individual => 'Individual',
            self::Corporate => 'Corporate',
            self::Government => 'Government',
        };
    }
}
