<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Companies';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected static function currentUser(): ?User
    {
        return Filament::auth()->user();
    }

    protected static function currentUserId(): ?int
    {
        return Filament::auth()->id();
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['owner', 'event', 'package', 'country', 'createdBy', 'bookedBy']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('company_name')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('status')
                ->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'meeting' => 'Meeting',
                    'negotiation' => 'Negotiation',
                    'won' => 'Won',
                    'lost' => 'Lost',
                ])
                ->required(),

            Forms\Components\Select::make('event_id')
                ->relationship('event', 'name')
                ->searchable()
                ->nullable(),

            Forms\Components\Select::make('package_id')
                ->relationship('package', 'name')
                ->searchable()
                ->nullable(),

            Forms\Components\Select::make('country_id')
                ->relationship('country', 'name')
                ->searchable()
                ->nullable(),

            Forms\Components\TextInput::make('contact_person')->maxLength(255)->nullable(),
            Forms\Components\TextInput::make('contact_mobile')->maxLength(50)->nullable(),
            Forms\Components\TextInput::make('contact_email')->email()->maxLength(255)->nullable(),

            Forms\Components\DatePicker::make('next_followup_date')->nullable(),

            Forms\Components\Textarea::make('notes')->columnSpanFull()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('owner.name')->label('Owner')->sortable(),
                Tables\Columns\TextColumn::make('booked')
                    ->label('Booked')
                    ->getStateUsing(function (Company $record) {
                        $currentUserId = Filament::auth()->id();
                        
                        if ($record->created_by === $currentUserId) {
                            return 'Your Company';
                        }
                        
                        if ($record->booked_by) {
                            if ($record->booked_by === $currentUserId) {
                                return 'Booked by You';
                            }
                            
                        }
                        
                        return '-';
                    })
                    ->badge()
                    ->color(fn (Company $record) => match (true) {
                        $record->created_by === Filament::auth()->id() => 'success',
                        $record->booked_by === Filament::auth()->id() => 'info',
                        $record->booked_by !== null => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('event.name')->label('Event')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country.name')->label('Country')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('next_followup_date')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'meeting' => 'Meeting',
                        'negotiation' => 'Negotiation',
                        'won' => 'Won',
                        'lost' => 'Lost',
                    ]),
                Tables\Filters\Filter::make('my_companies')
                    ->label('My Companies')
                    ->query(fn ($query) => $query->where('created_by', Filament::auth()->id()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
