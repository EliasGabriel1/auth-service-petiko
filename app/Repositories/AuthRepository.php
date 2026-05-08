<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByApiToken(string $token): ?User
    {
        return User::where('api_token', $token)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function updateApiToken(User $user, string $token): User
    {
        $user->api_token = $token;
        $user->save();

        return $user;
    }
}
