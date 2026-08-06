<?php

namespace App\Repositories;

use App\Models\Ad;

class AdRepository
{
    public function index($filters = [])
    {
        $query = Ad::query();

        if (!empty($filters['name'])) {
            $query->where('name->ar', 'like', '%' . $filters['name'] . '%')
                  ->orWhere('name->en', 'like', '%' . $filters['name'] . '%');
        }

        if (!is_null($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['from'])) {
            $query->whereDate('start_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('end_date', '<=', $filters['to']);
        }

        return $query->latest()->paginate(10);
    }

    public function create(array $data)
    {
        return Ad::create($data);
    }

    public function update(Ad $ad, array $data)
    {
        $ad->update($data);
        return $ad;
    }

    public function show($id)
    {
        return Ad::findOrFail($id);
    }

    public function toggleActive(Ad $ad)
    {
        $ad->active = !$ad->active;
        $ad->save();
        return $ad;
    }
}
