<?php
namespace App\Repositories;

use App\Models\Speaker;

class SpeakerRepository
{
    public function all()
    {
        return Speaker::all();
    }

    public function paginate($limit = 10)
    {
        return Speaker::latest()->paginate($limit);
    }

    public function create(array $data)
    {
        return Speaker::create($data);
    }

    public function update(Speaker $speaker, array $data)
    {
        return $speaker->update($data);
    }

    public function delete(Speaker $speaker)
    {
        return $speaker->delete();
    }
}
