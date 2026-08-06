<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.admin.pages.profile';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?int $navigationSort = 1000;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        try {
            $roleName = 'No role assigned';
            if ($user->role_id) {
                if (!$user->relationLoaded('role')) {
                    $user->load('role');
                }
                $roleName = $user->role?->name ?? 'No role assigned';
            }

            $this->form->fill([
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'role_display' => $roleName,
                'is_active' => $user->is_active ?? false,
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error loading profile')
                ->body('Unable to load your profile information.')
                ->danger()
                ->send();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Name'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Email')
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('role_display')
                            ->label('Role')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Toggle::make('is_active')
                            ->disabled()
                            ->label('Active Status'),
                    ]),

                Forms\Components\Section::make('Change Password')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->password()
                            ->label('Current Password')
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('new_password')
                            ->password()
                            ->label('New Password')
                            ->minLength(8)
                            ->dehydrated(false)
                            ->same('new_password_confirmation'),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->password()
                            ->label('Confirm New Password')
                            ->dehydrated(false),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            Notification::make()
                ->title('You must be logged in to update your profile.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                $this->form->getComponent('current_password')->addError('The current password is incorrect.');
                return;
            }

            $user->password = $data['new_password'];
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
        ]);

        $this->form->getComponent('current_password')->state(null);
        $this->form->getComponent('new_password')->state(null);
        $this->form->getComponent('new_password_confirmation')->state(null);

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
