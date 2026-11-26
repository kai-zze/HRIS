<?php

namespace App\Enums;

enum ApplicantStage: string
{
    case Applied = 'Applied';
    case Screening = 'Screening';
    case Interview = 'Interview';
    case Offer = 'Offer';
    case Hired = 'Hired';
    case Rejected = 'Rejected';

    public function getColor(): string
    {
        return match ($this) {
            self::Applied => 'gray',
            self::Screening => 'info',
            self::Interview => 'warning',
            self::Offer => 'primary',
            self::Hired => 'success',
            self::Rejected => 'danger',
        };
    }
}
