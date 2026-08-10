<?php

namespace App\Repositories;

use App\Models\MultimediaCategory;

class MultimediaCategoryRepository
{
    public function allPaginated($perPage = 10)
    {
        return MultimediaCategory::latest()->paginate($perPage);
    }

    public function find($id)
    {
        return MultimediaCategory::findOrFail($id);
    }

    public function create(array $data)
    {
        return MultimediaCategory::create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function toggleActive($id)
    {
        $category = $this->find($id);
        $category->active = !$category->active;
        $category->save();
        return $category;
    }
}
