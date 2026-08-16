<?php
namespace App\Repositories\Client;

use App\Models\Client;

interface ClientRepositoryInterface
{
    public function create(array $data): Client;
    public function findByEmail(string $email): ?Client;
    public function update(Client $client, array $data): Client;
    public function updatePassword(Client $client, string $plain): void;
    public function saveVerifyCode(Client $client, string $code6): void;
    public function clearVerifyCode(Client $client): void;
    public function saveResetCode(Client $client, string $code6): void;
    public function clearResetCode(Client $client): void;
}
