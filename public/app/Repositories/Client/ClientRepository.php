<?php
namespace App\Repositories\Client;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ClientRepository implements ClientRepositoryInterface
{
    public function create(array $data): Client
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return Client::create($data);
    }

    public function findByEmail(string $email): ?Client
    {
        return Client::where('email', $email)->first();
    }

    public function update(Client $client, array $data): Client
    {
        if (isset($data['password'])) unset($data['password']);
        $client->update($data);
        return $client->refresh();
    }

    public function updatePassword(Client $client, string $plain): void
    {
        $client->forceFill(['password' => Hash::make($plain)])->save();
    }

    public function saveVerifyCode(Client $client, string $code6): void
    {
        $client->verify_code_hash       = hash('sha256', $code6);
        $client->verify_code_expires_at = now()->addMinutes(20);
        $client->save();
    }

    public function clearVerifyCode(Client $client): void
    {
        $client->verify_code_hash = null;
        $client->verify_code_expires_at = null;
        $client->save();
    }

    public function saveResetCode(Client $client, string $code6): void
    {
        $client->reset_code_hash       = hash('sha256', $code6);
        $client->reset_code_expires_at = now()->addMinutes(20);
        $client->save();
    }

    public function clearResetCode(Client $client): void
    {
        $client->reset_code_hash = null;
        $client->reset_code_expires_at = null;
        $client->save();
    }
}
