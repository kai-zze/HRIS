<?php

namespace Database\Seeders;

use App\Enums\EntityCode;
use App\Models\Department;
use App\Models\Entity;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DepartmentPositionSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            EntityCode::Stc->value => [
                ['code' => 'TRG', 'name' => 'Training & Assessment Delivery', 'positions' => [
                    ['code' => 'TRG-DIR', 'title' => 'Training Director', 'grade' => 'MGR-2'],
                    ['code' => 'TRG-LEAD', 'title' => 'Lead TESDA Accredited Trainer', 'grade' => 'SUP-1'],
                    ['code' => 'TRG-INST', 'title' => 'Technical Instructor', 'grade' => 'JG-3'],
                    ['code' => 'TRG-ASSR', 'title' => 'Competency Assessor', 'grade' => 'JG-3'],
                ]],
                ['code' => 'REG', 'name' => 'Registrar & Compliance', 'positions' => [
                    ['code' => 'REG-HEAD', 'title' => 'School Registrar', 'grade' => 'SUP-1'],
                    ['code' => 'REG-SPEC', 'title' => 'TESDA Documentation Specialist', 'grade' => 'JG-2'],
                ]],
                ['code' => 'HRAD', 'name' => 'Human Resources & Admin', 'positions' => [
                    ['code' => 'HR-MGR', 'title' => 'Group HR Manager', 'grade' => 'MGR-1'],
                    ['code' => 'HR-GEN', 'title' => 'HR Generalist', 'grade' => 'JG-3'],
                ]],
                ['code' => 'FIN', 'name' => 'Finance & Accounting', 'positions' => [
                    ['code' => 'FIN-ACC', 'title' => 'Senior Accountant', 'grade' => 'SUP-1'],
                    ['code' => 'FIN-PAY', 'title' => 'Payroll & Billing Officer', 'grade' => 'JG-3'],
                ]],
                ['code' => 'ACAD', 'name' => 'Technical Curriculum & Instruction', 'positions' => [
                    ['code' => 'ACAD-HEAD', 'title' => 'Academic Supervisor', 'grade' => 'SUP-1'],
                    ['code' => 'ACAD-TRN', 'title' => 'Skills Trainer', 'grade' => 'JG-3'],
                    ['code' => 'ACAD-LAB', 'title' => 'Workshop & Lab Custodian', 'grade' => 'JG-2'],
                ]],
                ['code' => 'ADM', 'name' => 'Branch Administration', 'positions' => [
                    ['code' => 'ADM-HR', 'title' => 'Branch HR Coordinator', 'grade' => 'JG-3'],
                    ['code' => 'ADM-CLK', 'title' => 'Admin Assistant', 'grade' => 'JG-1'],
                ]],
            ],
            EntityCode::KnoahMari->value => [
                ['code' => 'OPS', 'name' => 'Client Operations', 'positions' => [
                    ['code' => 'OPS-MGR', 'title' => 'Operations Manager', 'grade' => 'MGR-1'],
                    ['code' => 'OPS-SUP', 'title' => 'Team Supervisor', 'grade' => 'SUP-1'],
                    ['code' => 'OPS-SPEC', 'title' => 'Operations Specialist', 'grade' => 'JG-2'],
                ]],
                ['code' => 'HRAD', 'name' => 'People & Culture', 'positions' => [
                    ['code' => 'HR-OFFICER', 'title' => 'HR Officer', 'grade' => 'JG-3'],
                ]],
                ['code' => 'MKT', 'name' => 'Business Development', 'positions' => [
                    ['code' => 'MKT-EXEC', 'title' => 'Account Executive', 'grade' => 'JG-3'],
                ]],
            ],
            EntityCode::RedGoose->value => [
                ['code' => 'LOG', 'name' => 'Logistics & Dispatch', 'positions' => [
                    ['code' => 'LOG-MGR', 'title' => 'Logistics Manager', 'grade' => 'MGR-1'],
                    ['code' => 'LOG-COOR', 'title' => 'Fleet & Dispatch Coordinator', 'grade' => 'JG-2'],
                    ['code' => 'LOG-DRV', 'title' => 'Delivery Specialist', 'grade' => 'JG-1'],
                ]],
                ['code' => 'WHSE', 'name' => 'Warehouse & Inventory', 'positions' => [
                    ['code' => 'WHSE-SUP', 'title' => 'Warehouse Supervisor', 'grade' => 'SUP-1'],
                    ['code' => 'WHSE-CLK', 'title' => 'Inventory Clerk', 'grade' => 'JG-1'],
                ]],
                ['code' => 'HRAD', 'name' => 'HR & Admin', 'positions' => [
                    ['code' => 'HR-SPEC', 'title' => 'HR & Timekeeping Specialist', 'grade' => 'JG-3'],
                ]],
            ],
            EntityCode::Petc->value => [
                ['code' => 'TECH', 'name' => 'Testing & Inspection Operations', 'positions' => [
                    ['code' => 'TECH-MGR', 'title' => 'Center Manager', 'grade' => 'MGR-1'],
                    ['code' => 'TECH-INSP', 'title' => 'Emission & Vehicle Inspector', 'grade' => 'JG-2'],
                    ['code' => 'TECH-ENC', 'title' => 'Data Encoder & Cashier', 'grade' => 'JG-1'],
                ]],
                ['code' => 'HRAD', 'name' => 'HR & Compliance', 'positions' => [
                    ['code' => 'HR-ADM', 'title' => 'HR & Compliance Officer', 'grade' => 'JG-3'],
                ]],
            ],
        ];

        foreach ($catalog as $entityCode => $departments) {
            $entity = Entity::query()->where('code', $entityCode)->first();

            if (! $entity) {
                continue;
            }

            foreach ($departments as $deptData) {
                $department = Department::withoutEntityScope()->updateOrCreate(
                    [
                        'entity_id' => $entity->id,
                        'code' => $deptData['code'],
                    ],
                    [
                        'name' => $deptData['name'],
                        'is_active' => true,
                    ],
                );

                foreach ($deptData['positions'] as $posData) {
                    Position::withoutEntityScope()->updateOrCreate(
                        [
                            'entity_id' => $entity->id,
                            'code' => $posData['code'],
                        ],
                        [
                            'department_id' => $department->id,
                            'title' => $posData['title'],
                            'job_grade' => $posData['grade'],
                            'is_active' => true,
                        ],
                    );
                }
            }
        }
    }
}
