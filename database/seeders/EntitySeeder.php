<?php

namespace Database\Seeders;

use App\Enums\EntityCode;
use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        $entities = [
            [
                'code' => EntityCode::Stc->value,
                'name' => EntityCode::Stc->defaultName(),
                'short_name' => EntityCode::Stc->shortName(),
                'logo_path' => 'entity-logos/km100-stc.jpg',
                'tin' => '009-412-881-000',
                'sss_employer_number' => '03-9281741-4',
                'philhealth_employer_number' => '01-900482173-2',
                'pagibig_employer_number' => '2041-8829-1102',
                'address' => 'Unit 402 KM100 Corporate Center, Quezon City, Metro Manila 1103',
                'contact_number' => '+63 2 8512 4100',
                'email' => 'hr.stc@km100group.ph',
                'is_active' => true,
            ],
            [
                'code' => EntityCode::KnoahMari->value,
                'name' => EntityCode::KnoahMari->defaultName(),
                'short_name' => EntityCode::KnoahMari->shortName(),
                'logo_path' => 'entity-logos/knoah-mari.jpg',
                'tin' => '010-284-552-000',
                'sss_employer_number' => '03-8841205-9',
                'philhealth_employer_number' => '01-882104932-6',
                'pagibig_employer_number' => '2041-7714-3390',
                'address' => '2F Ortigas Business Suites, Pasig City, Metro Manila 1605',
                'contact_number' => '+63 2 8634 7200',
                'email' => 'hr.knoahmari@km100group.ph',
                'is_active' => true,
            ],
            [
                'code' => EntityCode::RedGoose->value,
                'name' => EntityCode::RedGoose->defaultName(),
                'short_name' => EntityCode::RedGoose->shortName(),
                'logo_path' => 'entity-logos/red-goose.jpg',
                'tin' => '008-773-910-000',
                'sss_employer_number' => '03-7712049-1',
                'philhealth_employer_number' => '01-774920184-5',
                'pagibig_employer_number' => '2041-6192-4418',
                'address' => '84 Chino Roces Ave., Makati City, Metro Manila 1230',
                'contact_number' => '+63 2 8892 3310',
                'email' => 'hr.redgoose@km100group.ph',
                'is_active' => true,
            ],
            [
                'code' => EntityCode::Petc->value,
                'name' => EntityCode::Petc->defaultName(),
                'short_name' => EntityCode::Petc->shortName(),
                'logo_path' => 'entity-logos/km100-petc.jpg',
                'tin' => '010-519-008-000',
                'sss_employer_number' => '03-5519824-3',
                'philhealth_employer_number' => '01-553910284-8',
                'pagibig_employer_number' => '2041-4910-8825',
                'address' => '52 National Road, Calamba, Laguna 4027',
                'contact_number' => '+63 49 545 1900',
                'email' => 'hr.petc@km100group.ph',
                'is_active' => true,
            ],
        ];

        foreach ($entities as $data) {
            Entity::query()->updateOrCreate(
                ['code' => $data['code']],
                $data,
            );
        }
    }
}
