<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static array $statuses = [
        'Confirmed' => 'Confirmed',
        'Free' => 'Free',
        'Booked' => 'Booked',
    ];

    protected static function isAdmin(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return DB::table('user_role')
            ->join('roles', 'roles.id', '=', 'user_role.role_id')
            ->where('user_role.user_id', $user->id)
            ->where(function ($query) {
                $query->where('roles.slug', 'admin')
                    ->orWhere('roles.name', 'Admin')
                    ->orWhere('roles.id', 1);
            })
            ->exists();
    }

    protected static function isMine($record): bool
    {
        return Auth::id() && (int) $record->user_id === (int) Auth::id();
    }

    /**
     * Hide a company's private value from users who are neither the owner
     * nor an admin. Used to keep contact details private between users.
     */
    protected static function maskedValue($record, ?string $value): ?string
    {
        if (static::isAdmin() || static::isMine($record)) {
            return $value;
        }

        return '—';
    }

    protected static function visibleStatus($record): string
    {
        if (static::isAdmin() || static::isMine($record)) {
            return $record->status ?: 'Free';
        }

        return 'Booked';
    }

    public static function canViewAny(): bool
    {
        return Auth::check();
    }

    public static function canCreate(): bool
    {
        return static::isAdmin()
            || (Auth::user()?->hasPermission('create_companies') ?? false);
    }

    public static function canView($record): bool
    {
        return static::isAdmin() || static::isMine($record);
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if (static::isAdmin()) {
            return true;
        }

        return $user->hasPermission('update_companies')
            && static::isMine($record);
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if (static::isAdmin()) {
            return true;
        }

        return $user->hasPermission('delete_companies')
            && static::isMine($record);
    }

    public static function form(Form $form): Form
    {
        $isAdmin = static::isAdmin();

        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(static::$statuses)
                        ->required()
                        ->default('Free')
                        ->native(false),

                    Forms\Components\TextInput::make('company_name')
                        ->label('Company name')
                        ->required()
                        ->unique(table: Company::class, column: 'company_name', ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),
                ])
                ->columns(3),

            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('package_id')
                        ->label('Package')
                        ->relationship('package', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Forms\Components\Select::make('events')
                        ->label('Events')
                        ->relationship('events', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->native(false),
                ])
                ->columns(2),

            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('contact_person')
                        ->label('Contact person')
                        ->maxLength(255),

                    Forms\Components\Select::make('country_id')
                        ->label('Country')
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),
                ])
                ->columns(2),

            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('contact_email')
                        ->label('Contact email')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('contact_mobile')
                        ->label('Contact mobile')
                        ->tel()
                        ->maxLength(255),
                ])
                ->columns(2),

            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\DatePicker::make('next_followup_date')
                        ->label('Next followup date')
                        ->native(false)
                        ->displayFormat('m/d/Y'),

                    $isAdmin
                        ? Forms\Components\Select::make('user_id')
                            ->label('Assigned User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (!empty($state) && $get('status') !== 'Confirmed') {
                                    $set('status', 'Booked');
                                }

                                if (empty($state) && $get('status') === 'Booked') {
                                    $set('status', 'Free');
                                }
                            })
                        : Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id()),
                ])
                ->columns(2),

            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = static::isAdmin();

        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if (!$user || static::isAdmin()) {
                    return;
                }

                $query->select('companies.*')
                    ->selectRaw("
                        CASE
                            WHEN companies.user_id = ? THEN companies.status
                            ELSE 'Booked'
                        END as visible_status
                    ", [$user->id]);
            })
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('visible_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn ($record) => static::visibleStatus($record))
                    ->color(fn ($record): string => match (static::visibleStatus($record)) {
                        'Confirmed' => 'success',
                        'Free' => 'gray',
                        'Booked' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $user = Auth::user();

                        if (!$user || static::isAdmin()) {
                            return $query->where('companies.status', 'like', "%{$search}%");
                        }

                        if (stripos('Booked', $search) !== false || stripos('محجوزة', $search) !== false) {
                            return $query->where(function ($q) use ($user, $search) {
                                $q->where('companies.user_id', '!=', $user->id)
                                    ->orWhere('companies.status', 'like', "%{$search}%");
                            });
                        }

                        return $query->where('companies.user_id', $user->id)
                            ->where('companies.status', 'like', "%{$search}%");
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        $user = Auth::user();

                        if (!$user || static::isAdmin()) {
                            return $query->orderBy('companies.status', $direction);
                        }

                        return $query->orderByRaw("
                            CASE
                                WHEN companies.user_id = ? THEN companies.status
                                ELSE 'Booked'
                            END {$direction}
                        ", [$user->id]);
                    }),

                Tables\Columns\TextColumn::make('events.name')
                    ->label('Events')
                    ->badge()
                    ->separator(',')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact person')
                    ->formatStateUsing(fn ($state, $record) => static::maskedValue($record, $state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Contact email')
                    ->formatStateUsing(fn ($state, $record) => static::maskedValue($record, $state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('contact_mobile')
                    ->label('Contact mobile')
                    ->formatStateUsing(fn ($state, $record) => static::maskedValue($record, $state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('package.name')
                    ->label('Package')
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigned User')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: !$isAdmin),

                Tables\Columns\TextColumn::make('next_followup_date')
                    ->label('Next Followup')
                    ->date('m/d/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(array_filter([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(static::$statuses)
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        $user = Auth::user();

                        if (!$value) {
                            return $query;
                        }

                        if (!$user || static::isAdmin()) {
                            return $query->where('companies.status', $value);
                        }

                        if ($value === 'Booked') {
                            return $query->where(function ($q) use ($user) {
                                $q->where('companies.user_id', '!=', $user->id)
                                    ->orWhere(function ($sub) use ($user) {
                                        $sub->where('companies.user_id', $user->id)
                                            ->where('companies.status', 'Booked');
                                    });
                            });
                        }

                        return $query->where('companies.user_id', $user->id)
                            ->where('companies.status', $value);
                    }),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('events')
                    ->label('Event')
                    ->relationship('events', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('package_id')
                    ->label('Package')
                    ->relationship('package', 'name'),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name'),

                $isAdmin
                    ? Tables\Filters\SelectFilter::make('user_id')
                        ->label('Assigned User')
                        ->relationship('user', 'name')
                    : null,
            ]))
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn ($record) => static::isAdmin() || static::isMine($record)),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => static::canDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::isAdmin()),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['category', 'events', 'package', 'country', 'user'])
            ->whereNull('companies.deleted_at');

        if (!Auth::check()) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}