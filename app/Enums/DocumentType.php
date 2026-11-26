<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasColor, HasLabel
{
    case EmploymentContract = 'contract';
    case NbiClearance = 'nbi_clearance';
    case MedicalCertificate = 'medical_certificate';
    case GovernmentId = 'government_id';
    case TrainingCertificate = 'training_certificate';
    case Diploma = 'diploma';
    case Resume = 'resume';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::EmploymentContract => 'Employment Contract',
            self::NbiClearance => 'NBI Clearance',
            self::MedicalCertificate => 'Medical Certificate',
            self::GovernmentId => 'Government ID',
            self::TrainingCertificate => 'TESDA / Training Certificate',
            self::Diploma => 'Diploma / TOR',
            self::Resume => 'Resume / Bio-Data',
            self::Other => 'Other Document',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EmploymentContract => 'primary',
            self::NbiClearance, self::MedicalCertificate => 'warning',
            self::TrainingCertificate => 'success',
            default => 'gray',
        };
    }
}
