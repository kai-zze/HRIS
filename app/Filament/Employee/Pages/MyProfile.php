<?php

namespace App\Filament\Employee\Pages;

use App\Models\EmergencyContact;
use App\Models\Employee;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected static string | UnitEnum | null $navigationGroup = 'My Self-Service';

    protected static ?string $navigationLabel = 'My Profile & 201 File';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.employee.pages.my-profile';

    public ?array $data = [];

    public ?Employee $employee = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        if ($this->employee) {
            $this->form->fill([
                'contact_number' => $this->employee->contact_number,
                'residential_address' => $this->employee->residential_address,
                'permanent_address' => $this->employee->permanent_address,
                'emergency_contacts' => $this->employee->emergencyContacts->map(fn ($contact) => [
                    'name' => $contact->name,
                    'relationship' => $contact->relationship,
                    'contact_number' => $contact->contact_number,
                    'is_primary' => (bool) $contact->is_primary,
                ])->toArray(),
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Update Contact & Address Information')
                    ->description('Ensure your current residential address and contact number are up-to-date for HR and emergency notifications.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('contact_number')
                                ->label('Personal Mobile / Contact Number')
                                ->tel()
                                ->required()
                                ->maxLength(30)
                                ->placeholder('+63 917 123 4567'),
                            TextInput::make('email')
                                ->label('Work Email (Read-Only)')
                                ->disabled()
                                ->default($this->employee?->email),
                            Textarea::make('residential_address')
                                ->label('Current Residential Address')
                                ->rows(2)
                                ->required()
                                ->columnSpanFull(),
                            Textarea::make('permanent_address')
                                ->label('Permanent Provincial / Home Address')
                                ->rows(2)
                                ->required()
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Emergency Contacts')
                    ->description('Persons to contact in case of emergency.')
                    ->schema([
                        Repeater::make('emergency_contacts')
                            ->label('Emergency Contacts')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Contact Full Name')
                                    ->required(),
                                TextInput::make('relationship')
                                    ->label('Relationship')
                                    ->placeholder('e.g. Spouse, Parent, Sibling')
                                    ->required(),
                                TextInput::make('contact_number')
                                    ->label('Phone / Mobile Number')
                                    ->tel()
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Emergency Contact'),
                    ]),
            ]);
    }

    public function save(): void
    {
        if (! $this->employee) {
            Notification::make()
                ->title('No Employee Record Linked')
                ->body('Your user account is not yet linked to an active employee profile. Please contact HR.')
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();

        $this->employee->update([
            'contact_number' => $data['contact_number'],
            'residential_address' => $data['residential_address'],
            'permanent_address' => $data['permanent_address'],
        ]);

        // Sync emergency contacts
        if (isset($data['emergency_contacts'])) {
            $this->employee->emergencyContacts()->delete();
            foreach ($data['emergency_contacts'] as $contact) {
                if (! empty($contact['name'])) {
                    $this->employee->emergencyContacts()->create([
                        'name' => $contact['name'],
                        'relationship' => $contact['relationship'] ?? 'Family',
                        'contact_number' => $contact['contact_number'] ?? '',
                        'is_primary' => $contact['is_primary'] ?? false,
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Profile Updated Successfully')
            ->body('Your contact information and emergency contacts have been updated.')
            ->success()
            ->send();
    }
}
