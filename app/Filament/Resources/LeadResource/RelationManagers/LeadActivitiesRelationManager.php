<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LeadActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('sv.nav.audit');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label(__('sv.fields.created_at'))->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('activity_type')->label(__('sv.fields.action'))->badge(),
                Tables\Columns\TextColumn::make('message')->label(__('sv.fields.notes'))->wrap(),
                Tables\Columns\TextColumn::make('user.name')->label(__('sv.fields.sales_rep'))->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->headerActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
