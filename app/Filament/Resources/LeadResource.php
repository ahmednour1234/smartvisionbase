<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Country;
use App\Models\Event;
use App\Models\Lead;
use App\Models\LostCategory;
use App\Models\Package;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('sv.nav.leads');
    }

    

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('leads.view') ?? false;
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'isAdmin') ? $user->isAdmin() : (($user?->role) === 'admin');

        return $form
            ->schema([
                Forms\Components\Section::make(__('sv.sections.lead_details'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('sv.fields.company_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('event_id')
                            ->label(__('sv.fields.event'))
                            ->options(Event::query()->orderBy('sort_order')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('interested_package_id')
                            ->label(__('sv.fields.package'))
                            ->options(Package::query()->orderBy('sort_order')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label(__('sv.fields.status'))
                            ->options([
                                'new' => __('sv.status.new'),
                                'contacted' => __('sv.status.contacted'),
                                'meeting' => __('sv.status.meeting'),
                                'negotiation' => __('sv.status.negotiation'),
                                'won' => __('sv.status.won'),
                                'lost' => __('sv.status.lost'),
                            ])
                            ->required()
                            ->default('new'),

                        Forms\Components\Select::make('sales_rep_id')
                            ->label(__('sv.fields.sales_rep'))
                            ->options(User::query()->active()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->default(fn () => $user?->id)
                            ->disabled(! $isAdmin)
                            ->dehydrated(true)
                            ->nullable(),

                        Forms\Components\MultiSelect::make('countries')
                            ->label(__('sv.fields.countries'))
                            ->relationship('countries', 'name')
                            ->options(Country::query()->where('is_active', 1)->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ]),

                Forms\Components\Section::make(__('sv.sections.contact'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('contact_person')->label(__('sv.fields.contact_person'))->maxLength(120),
                        Forms\Components\TextInput::make('contact_mobile')->label(__('sv.fields.contact_mobile'))->maxLength(50),
                        Forms\Components\TextInput::make('contact_email')->label(__('sv.fields.contact_email'))->email()->maxLength(160),
                        Forms\Components\TextInput::make('contact_linkedin')->label(__('sv.fields.contact_linkedin'))->maxLength(255),
                        Forms\Components\TextInput::make('company_website')->label(__('sv.fields.company_website'))->maxLength(255),
                    ]),

                Forms\Components\Section::make(__('sv.sections.commercial'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('expected_value')
                            ->label(__('sv.fields.expected_value'))
                            ->numeric()
                            ->minValue(0)
                            ->nullable(),

                        Forms\Components\TextInput::make('currency')
                            ->label(__('sv.fields.currency'))
                            ->default('USD')
                            ->maxLength(3),

                        Forms\Components\TextInput::make('probability')
                            ->label(__('sv.fields.probability'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->nullable(),

                        Forms\Components\DatePicker::make('expected_close_date')
                            ->label(__('sv.fields.expected_close_date'))
                            ->nullable(),

                        Forms\Components\DatePicker::make('last_meeting')
                            ->label(__('sv.fields.last_meeting'))
                            ->nullable(),

                        Forms\Components\DatePicker::make('next_followup')
                            ->label(__('sv.fields.next_followup'))
                            ->nullable(),

                        Forms\Components\Textarea::make('lead_notes')
                            ->label(__('sv.fields.lead_notes'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('sv.sections.loss'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('lost_category_id')
                            ->label(__('sv.fields.lost_category'))
                            ->options(LostCategory::query()->orderBy('sort_order')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('lost_reason')
                            ->label(__('sv.fields.lost_reason'))
                            ->maxLength(255)
                            ->nullable(),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('status') === 'lost'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')->label(__('sv.fields.company_name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_person')->label(__('sv.fields.contact_person'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('contact_mobile')->label(__('sv.fields.contact_mobile'))->toggleable(),
                Tables\Columns\TextColumn::make('contact_email')->label(__('sv.fields.contact_email'))->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('sv.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? __('sv.status.' . $state) : $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('salesRep.name')->label(__('sv.fields.sales_rep'))->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('event.name')->label(__('sv.fields.event'))->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('next_followup')->label(__('sv.fields.next_followup'))->date()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('sv.fields.created_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('sv.fields.status'))
                    ->options([
                        'new' => __('sv.status.new'),
                        'contacted' => __('sv.status.contacted'),
                        'meeting' => __('sv.status.meeting'),
                        'negotiation' => __('sv.status.negotiation'),
                        'won' => __('sv.status.won'),
                        'lost' => __('sv.status.lost'),
                    ]),
                Tables\Filters\SelectFilter::make('event_id')
                    ->label(__('sv.fields.event'))
                    ->options(fn () => Event::query()->orderBy('sort_order')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MeetingsRelationManager::class,
            RelationManagers\LeadActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['salesRep', 'event']);

        $user = Auth::user();
        if ($user && method_exists($user, 'isAdmin') && ! $user->isAdmin()) {
            $query->where('sales_rep_id', $user->id);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('leads.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('leads.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('leads.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('leads.delete') ?? false;
    }
}
