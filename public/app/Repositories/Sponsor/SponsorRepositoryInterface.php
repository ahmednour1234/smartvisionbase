<?php

namespace App\Repositories\Sponsor;

use App\Models\Sponsor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SponsorRepositoryInterface
{
    public function findById(int $id): ?Sponsor;
    public function findByEmail(string $email): ?Sponsor;
    public function create(array $data): Sponsor;
    public function update(Sponsor $sponsor, array $data): Sponsor;
    public function updatePassword(Sponsor $sponsor, string $newPassword): Sponsor;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
