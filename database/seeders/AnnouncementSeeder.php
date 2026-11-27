<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::query()->where('email', 'superadmin@km100group.ph')->first();
        $km100Entity = Entity::query()->where('code', 'STC')->first();

        $announcements = [
            [
                'entity_id' => null, // Group-wide
                'author_id' => $superAdmin?->id,
                'title' => 'KM100 Group Policy: Biometrics Punctuality & Leave Filing Protocol',
                'category' => 'Policy',
                'is_pinned' => true,
                'published_at' => now()->subDays(3),
                'content' => '<p>Good day, KM100 Team,</p><p>Please be reminded of our group-wide timekeeping and leave management guidelines across all five operating entities:</p><ul><li><strong>Daily Punches:</strong> All staff must record biometric Time In and Time Out promptly. A standard 15-minute grace period applies to morning shifts.</li><li><strong>Leave Applications:</strong> Planned leaves must be filed via the Employee Portal at least <strong>3 working days</strong> in advance for supervisor endorsement. Emergency leaves must be filed within 24 hours of return.</li><li><strong>Overtime Pre-authorization:</strong> Overtime requests require supervisor pre-approval prior to the rendering of extra hours.</li></ul><p>Thank you for your continuous professionalism and dedication.</p>',
            ],
            [
                'entity_id' => null, // Group-wide
                'author_id' => $superAdmin?->id,
                'title' => 'Official Advisory: Philippine Regular & Special Holiday Operations',
                'category' => 'Holiday',
                'is_pinned' => false,
                'published_at' => now()->subDays(1),
                'content' => '<p>In observance of nationwide Philippine holidays, please note the operational schedules for regular and special non-working holidays:</p><p>Employees required to render work on special holidays shall receive premium holiday compensation in accordance with DOLE Labor Advisory guidelines. Department heads will release unit skeleton schedules accordingly.</p>',
            ],
            [
                'entity_id' => $km100Entity?->id,
                'author_id' => $superAdmin?->id,
                'title' => 'KM100 Skills & Training Center: Annual TESDA Institutional Assessment',
                'category' => 'Event',
                'is_pinned' => true,
                'published_at' => now()->subHours(12),
                'content' => '<p>Attention: Faculty, Assessors, and Administrative Staff of KM100 Skills and Training Center, Inc.:</p><p>The annual institutional compliance audit and competency assessment review is scheduled for next week. Please ensure all learner dossiers, trainer accreditation credentials, and attendance registries are properly filed in the Registrar archive.</p>',
            ],
            [
                'entity_id' => null,
                'author_id' => $superAdmin?->id,
                'title' => 'HRIS Self-Service Portal Rollout & 201 Verification',
                'category' => 'General',
                'is_pinned' => false,
                'published_at' => now()->subHours(2),
                'content' => '<p>We are pleased to introduce the KM100 Employee Self-Service Portal! Employees can now review personal 201 records, update emergency contacts, file leaves, request overtime, and monitor daily time records (DTR) in real time.</p><p>Please inspect your contact details and notify HR if any statutory IDs require correction.</p>',
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::query()->updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
