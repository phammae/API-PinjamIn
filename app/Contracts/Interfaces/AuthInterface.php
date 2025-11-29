<?php

namespace App\Contracts\Interfaces;
use App\Models\User;
interface AuthInterface
{
    // User Register
    public function register(array $data): mixed;

    // Verify OTP
    public function verifyOtp(string $userId, string $otp): mixed;

    // Resend OTP
    public function resendOtp(string $userId): mixed;

    // Login user
    public function login(string $email, string $password): mixed;

    // find user by id
    public function findUserById(string $userID): mixed;
}
