<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\MovementType;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\Position;
use App\Models\User;
use App\Services\EntityContextService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('201 File Sections')
                    ->tabs([
                        // Tab 1: Personal Info
                        Tab::make('Personal Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Identity & Demographics')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('employee_number')
                                            ->label('Employee ID Number')
                                            ->required()
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(function () {
                                                $activeId = app(EntityContextService::class)->activeEntityId() ?? 1;
                                                return Employee::generateEmployeeNumber($activeId);
                                            })
                                            ->extraInputAttributes(['class' => 'font-mono font-bold']),
                                        Select::make('entity_id')
                                            ->label('Entity')
                                            ->options(fn () => Entity::query()->pluck('short_name', 'id'))
                                            ->default(fn () => Auth::user()?->entity_id ?? app(EntityContextService::class)->activeEntityId() ?? 1)
                                            ->disabled(fn () => Auth::user() instanceof User && ! Auth::user()->isSuperAdmin())
                                            ->dehydrated()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                if ($state) {
                                                    $set('employee_number', Employee::generateEmployeeNumber((int) $state));
                                                    $set('department_id', null);
                                                    $set('position_id', null);
                                                }
                                            }),
                                        FileUpload::make('photo_path')
                                            ->label('201 Photo')
                                            ->avatar()
                                            ->image()
                                            ->directory('employee-photos'),
                                        TextInput::make('first_name')->required()->maxLength(100),
                                        TextInput::make('middle_name')->maxLength(100),
                                        TextInput::make('last_name')->required()->maxLength(100),
                                        TextInput::make('suffix')->maxLength(20)->placeholder('Jr., III'),
                                        DatePicker::make('birthdate')->required()->native(false),
                                        Select::make('sex')
                                            ->options(Gender::class)
                                            ->required(),
                                        Select::make('civil_status')
                                            ->options(CivilStatus::class)
                                            ->required(),
                                        TextInput::make('contact_number')->tel()->maxLength(50),
                                        TextInput::make('email')->email()->maxLength(150),
                                        Textarea::make('residential_address')->rows(2)->columnSpan(2),
                                        Textarea::make('permanent_address')->rows(2)->columnSpanFull(),
                                    ]),
                            ]),

                        // Tab 2: Employment Details
                        Tab::make('Employment Info')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Assignment & Position')
                                    ->columns(3)
                                    ->schema([
                                        Select::make('department_id')
                                            ->label('Department')
                                            ->options(fn (Get $get) => Department::withoutEntityScope()
                                                ->where('entity_id', $get('entity_id'))
                                                ->pluck('name', 'id'))
                                            ->searchable()
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('position_id', null)),
                                        Select::make('position_id')
                                            ->label('Position / Job Title')
                                            ->options(fn (Get $get) => Position::withoutEntityScope()
                                                ->where('entity_id', $get('entity_id'))
                                                ->when($get('department_id'), fn ($q, $dept) => $q->where('department_id', $dept))
                                                ->pluck('title', 'id'))
                                            ->searchable()
                                            ->required(),
                                        Select::make('supervisor_id')
                                            ->label('Immediate Supervisor')
                                            ->options(fn (Get $get) => Employee::withoutGlobalScopes()
                                                ->where('entity_id', $get('entity_id'))
                                                ->pluck('first_name', 'id'))
                                            ->searchable(),
                                        Select::make('employment_status')
                                            ->options(EmploymentStatus::class)
                                            ->default(EmploymentStatus::Probationary)
                                            ->required(),
                                        DatePicker::make('date_hired')->required()->native(false),
                                        DatePicker::make('regularization_date')->native(false),
                                        TextInput::make('work_schedule')
                                            ->default('Monday - Friday (8:00 AM - 5:00 PM)')
                                            ->required()
                                            ->columnSpan(2),
                                        TextInput::make('basic_salary')
                                            ->label('Basic Monthly Salary (PHP)')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->default(0)
                                            ->required(),
                                    ]),
                            ]),

                        // Tab 3: Statutory IDs (Data Privacy Act Encrypted)
                        Tab::make('Government IDs')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Section::make('Statutory & Banking (Encrypted at Rest)')
                                    ->description('Protected under RA 10173. Values are masked on directory lists.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('tin')
                                            ->label('BIR Tax Identification Number (TIN)')
                                            ->placeholder('000-000-000-000')
                                            ->extraInputAttributes(['class' => 'font-mono']),
                                        TextInput::make('sss_number')
                                            ->label('Social Security System (SSS) No.')
                                            ->placeholder('00-0000000-0')
                                            ->extraInputAttributes(['class' => 'font-mono']),
                                        TextInput::make('philhealth_number')
                                            ->label('PhilHealth Identification No.')
                                            ->placeholder('00-000000000-0')
                                            ->extraInputAttributes(['class' => 'font-mono']),
                                        TextInput::make('pagibig_number')
                                            ->label('Pag-IBIG / HDMF MID No.')
                                            ->placeholder('0000-0000-0000')
                                            ->extraInputAttributes(['class' => 'font-mono']),
                                        TextInput::make('bank_name')
                                            ->label('Payroll Bank Name')
                                            ->placeholder('BDO / BPI / Metrobank'),
                                        TextInput::make('bank_account_number')
                                            ->label('Bank Account Number')
                                            ->extraInputAttributes(['class' => 'font-mono']),
                                    ]),
                            ]),

                        // Tab 4: Emergency Contacts & Dependents
                        Tab::make('Contacts & Dependents')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Emergency Contacts')
                                    ->schema([
                                        Repeater::make('emergencyContacts')
                                            ->relationship()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('name')->required(),
                                                TextInput::make('relationship')->required(),
                                                TextInput::make('contact_number')->tel()->required(),
                                                TextInput::make('alternate_contact')->tel(),
                                                TextInput::make('address')->columnSpan(2),
                                            ]),
                                    ]),
                                Section::make('Family Dependents')
                                    ->schema([
                                        Repeater::make('dependents')
                                            ->relationship()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('name')->required(),
                                                TextInput::make('relationship')->required(),
                                                DatePicker::make('birthdate')->native(false),
                                                Toggle::make('is_tax_dependent')->label('Qualified Tax Dependent')->default(true),
                                            ]),
                                    ]),
                            ]),

                        // Tab 5: Education & Work History
                        Tab::make('Background')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Section::make('Education Background')
                                    ->schema([
                                        Repeater::make('educationRecords')
                                            ->relationship()
                                            ->columns(4)
                                            ->schema([
                                                Select::make('level')
                                                    ->options([
                                                        'College' => 'College / University',
                                                        'Vocational' => 'Vocational / TESDA',
                                                        'High School' => 'High School',
                                                        'Post-Graduate' => 'Post-Graduate / Masters',
                                                    ])->required(),
                                                TextInput::make('school_name')->required(),
                                                TextInput::make('degree_or_course')->placeholder('BS Computer Science'),
                                                TextInput::make('year_graduated')->placeholder('2022'),
                                            ]),
                                    ]),
                                Section::make('Previous Employment History')
                                    ->schema([
                                        Repeater::make('workHistories')
                                            ->relationship()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('company_name')->required(),
                                                TextInput::make('position_title')->required(),
                                                DatePicker::make('start_date')->required()->native(false),
                                                DatePicker::make('end_date')->native(false),
                                                TextInput::make('reason_for_leaving')->columnSpan(2),
                                            ]),
                                    ]),
                            ]),

                        // Tab 6: 201 Documents
                        Tab::make('201 Documents')
                            ->icon('heroicon-o-document-duplicate')
                            ->schema([
                                Section::make('Employee Uploaded Documents')
                                    ->description('Contracts, clearances, medical certificates, and TESDA certifications.')
                                    ->schema([
                                        Repeater::make('documents')
                                            ->relationship()
                                            ->columns(3)
                                            ->schema([
                                                Select::make('document_type')
                                                    ->options(DocumentType::class)
                                                    ->required(),
                                                TextInput::make('title')->required()->placeholder('e.g. 2026 NBI Clearance'),
                                                FileUpload::make('file_path')
                                                    ->label('Document File')
                                                    ->directory('employee-201-docs')
                                                    ->storeFileNamesIn('file_name'),
                                                DatePicker::make('issue_date')->native(false),
                                                DatePicker::make('expiry_date')->native(false),
                                                TextInput::make('remarks')->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),

                        // Tab 7: Movements (Transfers & Promotions)
                        Tab::make('Movements & History')
                            ->icon('heroicon-o-arrows-right-left')
                            ->schema([
                                Section::make('Inter-Company Transfers & Position History')
                                    ->description('Tracks movement across the 5 KM100 group companies, salary adjustments, and promotions.')
                                    ->schema([
                                        Repeater::make('movements')
                                            ->relationship()
                                            ->columns(3)
                                            ->schema([
                                                Select::make('movement_type')
                                                    ->options(MovementType::class)
                                                    ->required(),
                                                DatePicker::make('effective_date')->required()->native(false),
                                                Select::make('to_entity_id')
                                                    ->label('Transfer Destination Entity')
                                                    ->options(fn () => Entity::query()->pluck('short_name', 'id'))
                                                    ->searchable(),
                                                Select::make('to_department_id')
                                                    ->label('Target Department')
                                                    ->options(fn () => Department::withoutEntityScope()->pluck('name', 'id'))
                                                    ->searchable(),
                                                Select::make('to_position_id')
                                                    ->label('New Position')
                                                    ->options(fn () => Position::withoutEntityScope()->pluck('title', 'id'))
                                                    ->searchable(),
                                                TextInput::make('new_salary')
                                                    ->label('Adjusted Salary (PHP)')
                                                    ->numeric()
                                                    ->prefix('₱'),
                                                Textarea::make('remarks')->columnSpanFull()->rows(2),
                                            ]),
                                    ]),
                            ]),

                        // Tab 8: Offboarding
                        Tab::make('Offboarding')
                            ->icon('heroicon-o-arrow-right-start-on-rectangle')
                            ->schema([
                                Section::make('Clearance & Separation Checklist')
                                    ->columns(2)
                                    ->schema([
                                        DatePicker::make('separation_date')->native(false),
                                        Select::make('separation_type')
                                            ->options([
                                                'resignation' => 'Voluntary Resignation',
                                                'end_of_contract' => 'End of Contract / Probation',
                                                'termination' => 'Termination / Authorized Cause',
                                                'retirement' => 'Retirement',
                                            ]),
                                        Textarea::make('separation_reason')->rows(2)->columnSpanFull(),
                                        Toggle::make('clearance_completed')
                                            ->label('Company Clearance Signed & Completed')
                                            ->default(false),
                                        Select::make('final_pay_status')
                                            ->options([
                                                'pending' => 'Pending Computation',
                                                'processing' => 'Processing / Audit',
                                                'released' => 'Released to Employee',
                                            ])->default('pending'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
