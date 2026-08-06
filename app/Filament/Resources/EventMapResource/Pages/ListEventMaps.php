<?php

namespace App\Filament\Resources\EventMapResource\Pages;

use App\Filament\Resources\EventMapResource;
use App\Models\Event;
use App\Models\EventMap;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListEventMaps extends ListRecords
{
    protected static string $resource = EventMapResource::class;

    protected static string $view = 'filament.resources.event-map-resource.pages.list-event-maps';

    public ?int $selectedEventId = null;

    public Collection $booths;

    public function mount(): void
    {
        parent::mount();

        $this->selectedEventId = request()->integer('event_id')
            ?: Event::query()->orderBy('id')->value('id');

        $this->loadBooths();
    }

    public function updatedSelectedEventId(): void
    {
        $this->selectedEventId = (int) $this->selectedEventId;
        $this->loadBooths();
    }

    public function loadBooths(): void
    {
        if (!$this->selectedEventId) {
            $this->booths = collect(range(1, 100))->map(fn ($booth) => [
                'booth' => $booth,
                'map' => null,
            ]);

            return;
        }

        $maps = EventMap::query()
            ->with(['event', 'company.category'])
            ->where('event_id', $this->selectedEventId)
            ->whereNotNull('booth')
            ->get()
            ->keyBy('booth');

        $this->booths = collect(range(1, 100))->map(fn ($booth) => [
            'booth' => $booth,
            'map' => $maps->get($booth),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Company'),
        ];
    }

    public function getEventsProperty()
    {
        return Event::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }
    public function getSelectedEventProperty()
{
    if (!$this->selectedEventId) {
        return null;
    }

    return Event::find($this->selectedEventId);
}
}