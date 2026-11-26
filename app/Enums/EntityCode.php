<?php

namespace App\Enums;

enum EntityCode: string
{
    case Km100Stc = 'KM100-STC';
    case KnoahMari = 'KNOAH';
    case RedGoose = 'REDGOOSE';
    case Stc = 'STC';
    case Petc = 'PETC';

    public function defaultName(): string
    {
        return match ($this) {
            self::Km100Stc => 'KM100 Skills and Training Center, Inc.',
            self::KnoahMari => 'Knoah Mari',
            self::RedGoose => 'Red Goose',
            self::Stc => 'STC',
            self::Petc => 'PETC',
        };
    }

    public function shortName(): string
    {
        return match ($this) {
            self::Km100Stc => 'KM100 STC',
            self::KnoahMari => 'Knoah Mari',
            self::RedGoose => 'Red Goose',
            self::Stc => 'STC',
            self::Petc => 'PETC',
        };
    }
}
