<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function register(array $data): mixed
    {
        return $this->model->query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'is_verified' => false,
        ]);
    }

    public function verifyOtp(string $userId, string $otp): mixed
    {
        $user = $this->findUserById($userId);

        if (!$user) {
            return null;
        }

        if (!password_verify($otp, $user->otp_code)) {
            return null;
        }

        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'is_verified' => true,
        ]);

        return $user->fresh();
    }

    public function resendOtp(string $userId): mixed
    {
        return $this->findUserById($userId);
    }

    public function login(string $email, string $password): mixed
    {
        // Check apakah login menggunakan email atau phone
        $fieldType = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        $user = $this->model->query()->where($fieldType, $email)->first();

        // Validasi user dan password
        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    public function findUserById(string $userId): mixed
    {
        return $this->model->query()->find($userId);
    }

    public function updateOtp(mixed $user, string $otp, $expiresAt): bool
    {
        return $user->update([
            'otp_code' => bcrypt($otp),
            'otp_expires_at' => $expiresAt,
        ]);
    }
}