<?php

namespace App\Http\Handlers;

use App\Contracts\Repositories\AuthRepository;
use App\Services\OtpService;
use Carbon\Carbon;

class AuthHandler
{
    protected $repository;
    protected $otpService;

    public function __construct(AuthRepository $repository, OtpService $otpService)
    {
        $this->repository = $repository;
        $this->otpService = $otpService;
    }

    public function handleRegister(array $data): array
    {
        $data['phone'] = $this->otpService->formatPhoneNumber($data['phone']);

        $user = $this->repository->register($data);

        $otp = $this->otpService->generateOtp();
        $expiresAt = $this->otpService->getExpirationTime();

        $this->repository->updateOtp($user, $otp, $expiresAt);

        $this->otpService->sendOtp($data['phone'], $otp);

        return [
            'user_id' => $user->id,
            'phone' => $data['phone'], 
        ];
    }

    public function handlerVerifyOtp(string $userId, string $otp): array
    {
        $user = $this->repository->findUserById($userId);

        if (!$user) {
            throw new \Exception(trans('auth.user_not_found'));
        }

        if ($user->is_verified) {
            throw new \Exception(trans('auth.already_verified'));
        }

        if ($this->otpService->isExpired($user->otp_expires_at)) {
            throw new \Exception(trans('auth.otp_expired'));
        }

        $verifiedUser = $this->repository->verifyOtp($userId, $otp);

        if (!$verifiedUser) {
            throw new \Exception(trans('auth.invalid_otp'));
        }

        $token = $verifiedUser->createToken('api_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $verifiedUser,
        ];
    }

    public function handleResendOtp(string $userId): array
    {
        $user = $this->repository->findUserById($userId);

        if (!$user) {
            throw new \Exception(trans('auth.user_not_found'));
        }

        if ($user->is_verified) {
            throw new \Exception(trans('auth.already_verified'));
        }

        $otp = $this->otpService->generateOtp();
        $expiresAt = $this->otpService->getExpirationTime();

        $this->repository->updateOtp($user, $otp, $expiresAt);

        $this->otpService->sendOtp($user->phone, $otp);

        return [
            'phone' => $user->phone,
        ];
    }

    public function handleLogin(string $email, string $password): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $this->otpService->formatPhoneNumber($email);
        }

        $user = $this->repository->login($email, $password);

        if (!$user) {
            throw new \Exception(trans('auth.failed'));
        }

        if (!$user->is_verified) {
            throw new \Exception(trans('auth.not_verified'), 403);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
