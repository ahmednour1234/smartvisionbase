<?php

namespace App\Repositories\Speaker;

use App\Models\Speaker;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SpeakerRepositoryInterface
{
    public function findById(int $id): ?Speaker;
    public function findByEmail(string $email): ?Speaker;
    public function create(array $data): Speaker;
    public function update(Speaker $speaker, array $data): Speaker;
    public function updatePassword(Speaker $speaker, string $newPassword): Speaker;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
