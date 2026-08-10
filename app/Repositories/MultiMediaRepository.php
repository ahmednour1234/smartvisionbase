<?php

namespace App\Repositories;

use App\Models\MultiMedia;

class MultiMediaRepository
{
    public function allPaginated($perPage = 10)
    {
        return MultiMedia::latest()->paginate($perPage);
    }

    public function all()
    {
        return MultiMedia::latest()->get();
    }

    public function find($id)
    {
        return MultiMedia::findOrFail($id);
    }

    public function create(array $data)
    {
        return MultiMedia::create($data);
    }

    public function update($id, array $data)
    {
        $media = $this->find($id);
        $media->update($data);
        return $media;
    }

    public function delete($id)
    {
        $media = $this->find($id);
        return $media->delete();
    }

    public function toggleActive($id)
    {
        $media = $this->find($id);
        $media->active = !$media->active;
        $media->save();
        return $media;
    }
}
