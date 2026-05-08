<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(private AuthRepository $repository)
    {
    }

    public function register(array $data): array
    {
        $user = $this->repository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $this->generateToken();
        $this->repository->updateApiToken($user, $token);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->repository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        $token = $this->generateToken();
        $this->repository->updateApiToken($user, $token);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function me(string $token): ?User
    {
        return $this->repository->findByApiToken($token);
    }

    private function generateToken(): string
    {
        return Str::random(80);
    }
}
