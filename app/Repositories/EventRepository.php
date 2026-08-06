<?
namespace App\Repositories;

use App\Models\Event;

class EventRepository
{
    public function all()
    {
        return Event::latest()->get();
    }

    public function paginate($limit = 10)
    {
        return Event::latest()->paginate($limit);
    }

    public function create(array $data)
    {
        return Event::create($data);
    }

    public function update(Event $event, array $data)
    {
        return $event->update($data);
    }

    public function delete(Event $event)
    {
        return $event->delete();
    }
}
