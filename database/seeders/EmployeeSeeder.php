<?php

namespace Database\Seeders;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\MovementType;
use App\Models\Department;
use App\Models\Dependent;
use App\Models\EducationRecord;
use App\Models\EmergencyContact;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeMovement;
use App\Models\Entity;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkHistory;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $entities = Entity::all()->keyBy('code');

        $sampleEmployees = [
            // KM100 STC - Super Admin Executive Profile
            [
                'entity_code' => 'KM100-STC',
                'first_name' => 'Marco Antonio',
                'middle_name' => 'Gomez',
                'last_name' => 'Reyes',
                'birthdate' => '1982-05-18',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Married,
                'email' => 'superadmin@km100group.ph',
                'contact_number' => '+63 917 888 9900',
                'residential_address' => 'Ayala Alabang, Muntinlupa City',
                'dept_code' => 'HRAD',
                'pos_code' => 'HR-MGR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2018-01-15',
                'reg_date' => '2018-07-15',
                'salary' => 120000.00,
            ],
            // KM100 STC
            [
                'entity_code' => 'KM100-STC',
                'first_name' => 'Clarissa',
                'middle_name' => 'Santos',
                'last_name' => 'Mendoza',
                'birthdate' => '1988-04-12',
                'sex' => Gender::Female,
                'civil_status' => CivilStatus::Married,
                'email' => 'hr.km100@km100group.ph',
                'contact_number' => '+63 917 123 4567',
                'residential_address' => 'Brgy. Central, Quezon City',
                'dept_code' => 'HRAD',
                'pos_code' => 'HR-MGR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2020-01-15',
                'reg_date' => '2020-07-15',
                'salary' => 65000.00,
            ],
            [
                'entity_code' => 'KM100-STC',
                'first_name' => 'Rafael',
                'middle_name' => 'Dela Cruz',
                'last_name' => 'Villanueva',
                'birthdate' => '1985-09-20',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Married,
                'email' => 'manager.km100@km100group.ph',
                'contact_number' => '+63 918 234 5678',
                'residential_address' => 'Project 4, Quezon City',
                'dept_code' => 'TRG',
                'pos_code' => 'TRG-DIR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2019-06-01',
                'reg_date' => '2019-12-01',
                'salary' => 75000.00,
            ],
            [
                'entity_code' => 'KM100-STC',
                'first_name' => 'Angelo',
                'middle_name' => 'Reyes',
                'last_name' => 'Dela Cruz',
                'birthdate' => '1995-11-08',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Single,
                'email' => 'employee@km100group.ph',
                'contact_number' => '+63 922 345 6789',
                'residential_address' => 'Commonwealth, Quezon City',
                'dept_code' => 'TRG',
                'pos_code' => 'TRG-INST',
                'status' => EmploymentStatus::Regular,
                'hired' => '2022-03-01',
                'reg_date' => '2022-09-01',
                'salary' => 32000.00,
            ],
            [
                'entity_code' => 'KM100-STC',
                'first_name' => 'Bianca',
                'middle_name' => 'Cruz',
                'last_name' => 'Ramos',
                'birthdate' => '1998-02-14',
                'sex' => Gender::Female,
                'civil_status' => CivilStatus::Single,
                'email' => 'bianca.ramos@km100group.ph',
                'contact_number' => '+63 927 456 7890',
                'residential_address' => 'Cubao, Quezon City',
                'dept_code' => 'REG',
                'pos_code' => 'REG-SPEC',
                'status' => EmploymentStatus::Probationary,
                'hired' => '2026-08-01',
                'reg_date' => null,
                'salary' => 25000.00,
            ],

            // Knoah Mari
            [
                'entity_code' => 'KNOAH',
                'first_name' => 'Patricia',
                'middle_name' => 'Alcantara',
                'last_name' => 'Santos',
                'birthdate' => '1990-07-25',
                'sex' => Gender::Female,
                'civil_status' => CivilStatus::Single,
                'email' => 'hr.knoah@km100group.ph',
                'contact_number' => '+63 917 567 8901',
                'residential_address' => 'San Antonio, Pasig City',
                'dept_code' => 'HRAD',
                'pos_code' => 'HR-OFFICER',
                'status' => EmploymentStatus::Regular,
                'hired' => '2021-02-15',
                'reg_date' => '2021-08-15',
                'salary' => 45000.00,
            ],
            [
                'entity_code' => 'KNOAH',
                'first_name' => 'Gabriel',
                'middle_name' => 'Torres',
                'last_name' => 'Bautista',
                'birthdate' => '1987-12-05',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Married,
                'email' => 'manager.knoah@km100group.ph',
                'contact_number' => '+63 919 678 9012',
                'residential_address' => 'Kapitolyo, Pasig City',
                'dept_code' => 'OPS',
                'pos_code' => 'OPS-MGR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2020-05-10',
                'reg_date' => '2020-11-10',
                'salary' => 68000.00,
            ],
            [
                'entity_code' => 'KNOAH',
                'first_name' => 'Irene',
                'middle_name' => 'Lopez',
                'last_name' => 'Castillo',
                'birthdate' => '1996-03-30',
                'sex' => Gender::Female,
                'civil_status' => CivilStatus::Single,
                'email' => 'irene.castillo@km100group.ph',
                'contact_number' => '+63 920 789 0123',
                'residential_address' => 'Rosario, Pasig City',
                'dept_code' => 'OPS',
                'pos_code' => 'OPS-SPEC',
                'status' => EmploymentStatus::Regular,
                'hired' => '2023-01-10',
                'reg_date' => '2023-07-10',
                'salary' => 28000.00,
            ],

            // Red Goose
            [
                'entity_code' => 'REDGOOSE',
                'first_name' => 'Jerome',
                'middle_name' => 'Navarro',
                'last_name' => 'Aquino',
                'birthdate' => '1992-08-18',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Married,
                'email' => 'hr.redgoose@km100group.ph',
                'contact_number' => '+63 917 890 1234',
                'residential_address' => 'Poblacion, Makati City',
                'dept_code' => 'HRAD',
                'pos_code' => 'HR-SPEC',
                'status' => EmploymentStatus::Regular,
                'hired' => '2021-09-01',
                'reg_date' => '2022-03-01',
                'salary' => 42000.00,
            ],
            [
                'entity_code' => 'REDGOOSE',
                'first_name' => 'Danilo',
                'middle_name' => 'Esteban',
                'last_name' => 'Pascual',
                'birthdate' => '1984-05-14',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Married,
                'email' => 'manager.redgoose@km100group.ph',
                'contact_number' => '+63 918 901 2345',
                'residential_address' => 'San Lorenzo, Makati City',
                'dept_code' => 'LOG',
                'pos_code' => 'LOG-MGR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2018-11-01',
                'reg_date' => '2019-05-01',
                'salary' => 70000.00,
            ],
            [
                'entity_code' => 'REDGOOSE',
                'first_name' => 'Paulo',
                'middle_name' => 'Morales',
                'last_name' => 'Salazar',
                'birthdate' => '1994-10-10',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Single,
                'email' => 'paulo.salazar@km100group.ph',
                'contact_number' => '+63 921 012 3456',
                'residential_address' => 'Bangkal, Makati City',
                'dept_code' => 'LOG',
                'pos_code' => 'LOG-COOR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2022-07-15',
                'reg_date' => '2023-01-15',
                'salary' => 30000.00,
            ],

            // STC
            [
                'entity_code' => 'STC',
                'first_name' => 'Marites',
                'middle_name' => 'Velasco',
                'last_name' => 'Navarro',
                'birthdate' => '1989-01-22',
                'sex' => Gender::Female,
                'civil_status' => CivilStatus::Married,
                'email' => 'hr.stc@km100group.ph',
                'contact_number' => '+63 917 111 2233',
                'residential_address' => 'Guinhawa, Malolos, Bulacan',
                'dept_code' => 'ADM',
                'pos_code' => 'ADM-HR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2021-04-01',
                'reg_date' => '2021-10-01',
                'salary' => 40000.00,
            ],
            [
                'entity_code' => 'STC',
                'first_name' => 'Rolando',
                'middle_name' => 'Perez',
                'last_name' => 'Cruz',
                'birthdate' => '1986-06-17',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Married,
                'email' => 'manager.stc@km100group.ph',
                'contact_number' => '+63 918 222 3344',
                'residential_address' => 'Tikay, Malolos, Bulacan',
                'dept_code' => 'ACAD',
                'pos_code' => 'ACAD-HEAD',
                'status' => EmploymentStatus::Regular,
                'hired' => '2019-08-15',
                'reg_date' => '2020-02-15',
                'salary' => 60000.00,
            ],

            // PETC
            [
                'entity_code' => 'PETC',
                'first_name' => 'Camille',
                'middle_name' => 'Gutierrez',
                'last_name' => 'Soriano',
                'birthdate' => '1993-12-03',
                'sex' => Gender::Female,
                'civil_status' => CivilStatus::Single,
                'email' => 'hr.petc@km100group.ph',
                'contact_number' => '+63 917 333 4455',
                'residential_address' => 'Real, Calamba, Laguna',
                'dept_code' => 'HRAD',
                'pos_code' => 'HR-ADM',
                'status' => EmploymentStatus::Regular,
                'hired' => '2022-01-10',
                'reg_date' => '2022-07-10',
                'salary' => 38000.00,
            ],
            [
                'entity_code' => 'PETC',
                'first_name' => 'Noel',
                'middle_name' => 'Bautista',
                'last_name' => 'Mercado',
                'birthdate' => '1983-09-29',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Married,
                'email' => 'manager.petc@km100group.ph',
                'contact_number' => '+63 918 444 5566',
                'residential_address' => 'Halang, Calamba, Laguna',
                'dept_code' => 'TECH',
                'pos_code' => 'TECH-MGR',
                'status' => EmploymentStatus::Regular,
                'hired' => '2018-03-01',
                'reg_date' => '2018-09-01',
                'salary' => 65000.00,
            ],
            [
                'entity_code' => 'PETC',
                'first_name' => 'Bryan',
                'middle_name' => 'Salas',
                'last_name' => 'Evangelista',
                'birthdate' => '1997-05-19',
                'sex' => Gender::Male,
                'civil_status' => CivilStatus::Single,
                'email' => 'bryan.evangelista@km100group.ph',
                'contact_number' => '+63 922 555 6677',
                'residential_address' => 'Turbina, Calamba, Laguna',
                'dept_code' => 'TECH',
                'pos_code' => 'TECH-INSP',
                'status' => EmploymentStatus::Regular,
                'hired' => '2023-04-01',
                'reg_date' => '2023-10-01',
                'salary' => 26000.00,
            ],
        ];

        $counter = 1;
        foreach ($sampleEmployees as $data) {
            $entity = $entities->get($data['entity_code']);
            if (! $entity) {
                continue;
            }

            $user = User::query()->where('email', $data['email'])->first();
            $department = Department::withoutEntityScope()
                ->where('entity_id', $entity->id)
                ->where('code', $data['dept_code'])
                ->first();

            $position = Position::withoutEntityScope()
                ->where('entity_id', $entity->id)
                ->where('code', $data['pos_code'])
                ->first();

            $empNumber = sprintf('%s-2026-%04d', $entity->code, $counter++);

            $employee = Employee::withoutGlobalScopes()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'user_id' => $user?->id,
                    'entity_id' => $entity->id,
                    'employee_number' => $empNumber,
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'],
                    'last_name' => $data['last_name'],
                    'birthdate' => $data['birthdate'],
                    'sex' => $data['sex'],
                    'civil_status' => $data['civil_status'],
                    'contact_number' => $data['contact_number'],
                    'residential_address' => $data['residential_address'],
                    'permanent_address' => $data['residential_address'],
                    'tin' => '123-456-789-000',
                    'sss_number' => '34-5678901-2',
                    'philhealth_number' => '12-345678901-2',
                    'pagibig_number' => '1234-5678-9012',
                    'bank_name' => 'BDO Unibank',
                    'bank_account_number' => '001234567890',
                    'department_id' => $department?->id,
                    'position_id' => $position?->id,
                    'employment_status' => $data['status'],
                    'date_hired' => $data['hired'],
                    'regularization_date' => $data['reg_date'],
                    'basic_salary' => $data['salary'],
                ]
            );

            // Emergency Contacts
            EmergencyContact::updateOrCreate(
                ['employee_id' => $employee->id, 'name' => "Emergency Contact for {$employee->first_name}"],
                [
                    'relationship' => 'Spouse / Parent',
                    'contact_number' => '+63 917 000 0000',
                    'address' => $employee->residential_address,
                ]
            );

            // Dependents
            if ($employee->civil_status === CivilStatus::Married) {
                Dependent::updateOrCreate(
                    ['employee_id' => $employee->id, 'name' => "Child of {$employee->first_name}"],
                    [
                        'relationship' => 'Child',
                        'birthdate' => '2018-05-10',
                        'is_tax_dependent' => true,
                    ]
                );
            }

            // Education Record
            EducationRecord::updateOrCreate(
                ['employee_id' => $employee->id, 'school_name' => 'University of the Philippines / PUP'],
                [
                    'level' => 'College',
                    'degree_or_course' => 'Bachelor of Science',
                    'year_graduated' => '2015',
                ]
            );

            // Work History
            WorkHistory::updateOrCreate(
                ['employee_id' => $employee->id, 'company_name' => 'Previous Philippine Corp.'],
                [
                    'position_title' => 'Junior Associate',
                    'start_date' => '2016-01-01',
                    'end_date' => '2018-12-31',
                    'reason_for_leaving' => 'Career growth & advancement',
                ]
            );

            // Documents with various expiration states
            EmployeeDocument::updateOrCreate(
                ['employee_id' => $employee->id, 'title' => 'Employment Contract (Signed)'],
                [
                    'document_type' => DocumentType::EmploymentContract,
                    'issue_date' => $employee->date_hired,
                    'expiry_date' => null,
                ]
            );

            // Add an expiring/expired document for demonstration
            $expiryOffset = ($counter % 2 === 0) ? 15 : 45; // some within 15 days, some within 45 days
            EmployeeDocument::updateOrCreate(
                ['employee_id' => $employee->id, 'title' => 'Medical Clearance Certificate'],
                [
                    'document_type' => DocumentType::MedicalCertificate,
                    'issue_date' => now()->subYear(),
                    'expiry_date' => now()->addDays($expiryOffset),
                    'remarks' => 'Annual medical checkup due for renewal.',
                ]
            );

            // Add sample transfer movement for demonstration
            if ($data['first_name'] === 'Angelo') {
                EmployeeMovement::updateOrCreate(
                    ['employee_id' => $employee->id, 'movement_type' => MovementType::Regularization],
                    [
                        'from_entity_id' => $entity->id,
                        'to_entity_id' => $entity->id,
                        'from_department_id' => $department?->id,
                        'to_department_id' => $department?->id,
                        'from_position_id' => $position?->id,
                        'to_position_id' => $position?->id,
                        'previous_salary' => 28000.00,
                        'new_salary' => 32000.00,
                        'effective_date' => '2022-09-01',
                        'remarks' => 'Regularization after 6 months probationary review.',
                    ]
                );
            }
        }
    }
}
