<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class UpcomingFollowUps extends BaseWidget
{
    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('sv.widgets.upcoming_followups');
    }

    protected function getTableQuery(): Builder
    {
        $from = Carbon::today();
        $to = Carbon::today()->addDays(7);

        $query = Lead::query()
            ->whereNotNull('next_followup')
            ->whereBetween('next_followup', [$from->toDateString(), $to->toDateString()]);

        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && ! $user->hasRole('admin')) {
            $query->where('sales_rep_id', $user->id);
        }

        return $query->orderBy('next_followup');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('company_name')->label(__('sv.fields.company_name'))->searchable(),
                Tables\Columns\TextColumn::make('contact_person')->label(__('sv.fields.contact_person')),
                Tables\Columns\TextColumn::make('status')->label(__('sv.fields.status'))->badge(),
                Tables\Columns\TextColumn::make('next_followup')->label(__('sv.fields.next_followup'))->date(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label(__('sv.actions.open'))
                    ->url(fn (Lead $record) => route('filament.admin.resources.leads.view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
