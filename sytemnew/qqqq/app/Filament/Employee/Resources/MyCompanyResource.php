<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\MyCompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MyCompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationLabel = 'Leads (All)';

    protected static ?string $recordTitleAttribute = 'company_name';

    private static function canSeeSensitive(Company $record): bool
    {
        $user = Auth::user();
        if ($user && $user->role && in_array($user->role->slug, ['admin', 'manager','sales'], true)) {
            return true;
        }

        return $record->owner_id === Auth::id();
    }

    private static function redact(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return 'HIDDEN';
    }

    private static function maskMobile(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if (strlen($digits) <= 4) {
            return 'HIDDEN';
        }

        return Str::mask($digits, '*', 2, max(strlen($digits) - 4, 0));
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $suffix = self::canSeeSensitive($record) ? '[OWNED]' : '[LOCKED]';

        return $record->company_name . ' ' . $suffix;
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        return ($record->owner_id === Auth::id())
            ? static::getUrl('edit', ['record' => $record])
            : null;
    }

    public static function getEloquentQuery(): Builder
    {
        // Sales must see all leads; sensitive fields are redacted in the UI for non-owned leads.
        return parent::getEloquentQuery()->with(['owner', 'event', 'package', 'country']);
}

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('company_name')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('event_id')
                ->relationship('event', 'name')
                ->searchable(),

            Forms\Components\Select::make('package_id')
                ->relationship('package', 'name')
                ->searchable(),

            Forms\Components\Select::make('country_id')
                ->relationship('country', 'name')
                ->searchable(),

            Forms\Components\TextInput::make('contact_person')
                ->maxLength(255),

            Forms\Components\TextInput::make('contact_mobile')
                ->tel()
                ->maxLength(50),

            Forms\Components\TextInput::make('contact_email')
                ->email()
                ->maxLength(255),

            Forms\Components\DatePicker::make('next_followup_date'),

            Forms\Components\Textarea::make('notes')
                ->rows(4),

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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owned By')
                    ->placeholder('UNCLAIMED'),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact')
                    ->getStateUsing(fn (Company $record): ?string => self::canSeeSensitive($record) ? $record->contact_person : self::redact($record->contact_person))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('contact_mobile')
                    ->label('Mobile')
                    ->getStateUsing(fn (Company $record): ?string => self::canSeeSensitive($record) ? $record->contact_mobile : self::maskMobile($record->contact_mobile))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Email')
                    ->getStateUsing(fn (Company $record): ?string => self::canSeeSensitive($record) ? $record->contact_email : self::redact($record->contact_email))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('next_followup_date')
                    ->date()
                    ->label('Next Follow-up')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('claim')
                    ->label('Claim')
                    ->requiresConfirmation()
                    ->visible(fn (Company $record): bool => $record->owner_id === null)
                    ->action(function (Company $record): void {
                        $userId = Auth::id();

                        DB::transaction(function () use ($record, $userId): void {
                            // Lock the current user row to serialize claim operations per-agent (prevents exceeding 60 under concurrency).
                            DB::table('users')->where('id', $userId)->lockForUpdate()->first();

                            $leadCount = Company::query()->where('owner_id', $userId)->count();
                            if ($leadCount >= 60) {
                                Notification::make()
                                    ->title('Limit reached (60 leads).')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            // Atomic claim prevents two agents from claiming the same lead.
                            $affected = Company::query()
                                ->whereKey($record->id)
                                ->whereNull('owner_id')
                                ->update([
                                    'owner_id' => $userId,
                                    'status' => 'contacted',
                                    'updated_at' => now(),
                                ]);

                            if ($affected === 1) {
                                Notification::make()
                                    ->title('Claimed successfully.')
                                    ->success()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Claim failed (already claimed).')
                                ->danger()
                                ->send();
                        });
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn (Company $record): bool => $record->owner_id === Auth::id()),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyCompanies::route('/'),
            'create' => Pages\CreateMyCompany::route('/create'),
            'edit' => Pages\EditMyCompany::route('/{record}/edit'),
        ];
    }
}
