<?php

namespace App\Repositories\Speaker;

use App\Models\Speaker;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSpeakerRepository implements SpeakerRepositoryInterface
{
    public function findById(int $id): ?Speaker
    {
        return Speaker::query()->find($id);
    }

    public function findByEmail(string $email): ?Speaker
    {
        return Speaker::query()->where('email', $email)->first();
    }

    public function create(array $data): Speaker
    {
        return Speaker::query()->create($data);
    }

    public function update(Speaker $speaker, array $data): Speaker
    {
        $speaker->update($data);
        return $speaker->refresh();
    }

    public function updatePassword(Speaker $speaker, string $newPassword): Speaker
    {
        $speaker->password = $newPassword; // سيتعمل Hash من الـ Mutator
        $speaker->save();
        return $speaker->refresh();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = Speaker::query();

        if (!empty($filters['q'])) {
            $kw = $filters['q'];
            $q->where(function ($x) use ($kw) {
                $x->where('name_en', 'like', "%$kw%")
                  ->orWhere('name_ar', 'like', "%$kw%")
                  ->orWhere('email', 'like', "%$kw%");
            });
        }

        if (!empty($filters['country_code'])) {
            $q->where('country_code', $filters['country_code']);
        }

        return $q->latest()->paginate($perPage);
    }
}
