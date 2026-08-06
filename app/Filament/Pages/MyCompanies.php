<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CompanyResource;
use App\Models\Company;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MyCompanies extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'My Companies';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.my-companies';

    protected static array $statuses = [
        'Confirmed' => 'Confirmed',
        'Free' => 'Free',
        'Booked' => 'Booked',
    ];

    public static function canAccess(array $parameters = []): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        $canViewBooked = $user?->hasPermission('view_booked_companies') ?? false;

        return $table
            ->query(
                Company::query()
                    ->with(['category', 'events', 'package', 'country', 'user'])
                    ->where('user_id', Auth::id())
            )
            ->modifyQueryUsing(function (Builder $query) {
                $query->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]);
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

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Confirmed' => 'success',
                        'Free' => 'gray',
                        'Booked' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('events.name')
                    ->label('Events')
                    ->badge()
                    ->separator(',')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact person')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Contact email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contact_mobile')
                    ->label('Contact mobile')
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
                    ->toggleable(isToggledHiddenByDefault: !$canViewBooked)
                    ->visible(fn () => $canViewBooked),

                Tables\Columns\TextColumn::make('next_followup_date')
                    ->label('Next Followup')
                    ->date('m/d/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('status')
                    ->options(static::$statuses),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('package_id')
                    ->label('Package')
                    ->relationship('package', 'name'),

                Tables\Filters\SelectFilter::make('events')
                    ->label('Event')
                    ->relationship('events', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => CompanyResource::getUrl('view', ['record' => $record])),

                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => CompanyResource::getUrl('edit', ['record' => $record])),

                Tables\Actions\DeleteAction::make()
                    ->label('Delete Company')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->successNotificationTitle('Company deleted successfully'),

                Tables\Actions\Action::make('unassign')
                    ->label('Unassign')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'user_id' => null,
                            'status' => 'Free',
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Company unassigned')
                            ->body('The company has been unassigned from you.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add')
                    ->url(CompanyResource::getUrl('create')),
            ]);
    }
}