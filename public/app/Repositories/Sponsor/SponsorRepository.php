<?php

namespace App\Repositories\Sponsor;

use App\Models\Sponsor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class SponsorRepository implements SponsorRepositoryInterface
{
    public function __construct(private Sponsor $model) {}

    public function findById(int $id): ?Sponsor
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?Sponsor
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): Sponsor
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->model->create($data);
    }

    public function update(Sponsor $sponsor, array $data): Sponsor
    {
        // لا تسمح بتغيير الإيميل إلى موجود مسبقًا (اترك الفاليديشن للـRequest عادة)
        if (array_key_exists('password', $data)) {
            unset($data['password']); // تغيير الباسورد من خلال updatePassword فقط
        }
        $sponsor->fill($data)->save();
        return $sponsor->refresh();
    }

    public function updatePassword(Sponsor $sponsor, string $newPassword): Sponsor
    {
        $sponsor->password = Hash::make($newPassword);
        $sponsor->save();
        return $sponsor->refresh();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = $this->model->newQuery();

        if (!empty($filters['name'])) {
            $q->where(function ($qq) use ($filters) {
                $qq->where('name_en', 'like', '%'.$filters['name'].'%')
                   ->orWhere('name_ar', 'like', '%'.$filters['name'].'%');
            });
        }

        if (!empty($filters['email'])) {
            $q->where('email', 'like', '%'.$filters['email'].'%');
        }

        return $q->latest('id')->paginate($perPage);
    }
}
