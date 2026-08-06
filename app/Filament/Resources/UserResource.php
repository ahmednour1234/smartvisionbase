<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('sv.nav.users');
    }

    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('users.manage') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('users.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label(__('sv.fields.name'))->required()->maxLength(120),
            Forms\Components\TextInput::make('email')->label(__('sv.fields.email'))->required()->email()->maxLength(160),
            Forms\Components\Select::make('role')
                ->label(__('sv.fields.role'))
                ->options(['admin' => 'admin', 'staff' => 'staff'])
                ->required()
                ->default('staff'),
            Forms\Components\Toggle::make('is_active')->label(__('sv.fields.is_active'))->default(true),
            Forms\Components\TextInput::make('password_hash')
                ->label(__('sv.fields.password'))
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->helperText('Leave empty to keep current password.'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->badge()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('must_change_password')->boolean()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('users.manage') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('users.manage') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('users.manage') ?? false;
    }
}
