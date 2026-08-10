<?php
namespace App\Repositories;

use App\Models\Sponsor;

class SponsorRepository
{
    public function all()
    {
        return Sponsor::all();
    }

    public function filter($request)
    {
        return Sponsor::when($request->name, function ($q) use ($request) {
                    $q->where('name_ar', 'like', "%{$request->name}%")
                      ->orWhere('name_en', 'like', "%{$request->name}%");
                })
                ->when($request->category_sponsor_id, function ($q) use ($request) {
                    $q->where('category_sponsor_id', $request->category_sponsor_id);
                })
                ->when($request->filled('active'), function ($q) use ($request) {
                    $q->where('active', $request->active);
                })
                ->latest()
                ->paginate(10);
    }

    public function create(array $data)
    {
        return Sponsor::create($data);
    }

    public function update(Sponsor $sponsor, array $data)
    {
        return $sponsor->update($data);
    }

    public function delete(Sponsor $sponsor)
    {
        return $sponsor->delete();
    }
}
