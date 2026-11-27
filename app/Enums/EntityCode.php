<?php

namespace App\Enums;

enum EntityCode: string
{
    case Stc = 'STC';
    case KnoahMari = 'KNOAH';
    case RedGoose = 'REDGOOSE';
    case Petc = 'PETC';

    public function defaultName(): string
    {
        return match ($this) {
            self::Stc => 'KM100 Skills and Training Center, Inc.',
            self::KnoahMari => 'Knoah Mari',
            self::RedGoose => 'Red Goose',
            self::Petc => 'KM100 PETC Private Emission Testing Center',
        };
    }

    public function shortName(): string
    {
        return match ($this) {
            self::Stc => 'STC',
            self::KnoahMari => 'Knoah Mari',
            self::RedGoose => 'Red Goose',
            self::Petc => 'PETC',
        };
    }
}
