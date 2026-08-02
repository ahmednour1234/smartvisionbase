<?php
namespace App\Repositories;

use App\Models\SponsorCategory;

class SponsorCategoryRepository
{
    public function all()
    {
        return SponsorCategory::all();
    }

    public function paginate($limit = 10)
    {
        return SponsorCategory::latest()->paginate($limit);
    }

    public function create(array $data)
    {
        return SponsorCategory::create($data);
    }

    public function update(SponsorCategory $category, array $data)
    {
        return $category->update($data);
    }

    public function delete(SponsorCategory $category)
    {
        return $category->delete();
    }
}
