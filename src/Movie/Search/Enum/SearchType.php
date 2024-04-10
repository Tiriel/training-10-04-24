<?php

namespace App\Movie\Search\Enum;

enum SearchType
{
    case Id;
    case Title;

    public function getApiParam(): string
    {
        return match ($this) {
            self::Id => 'i',
            self::Title => 't',
        };
    }
}
