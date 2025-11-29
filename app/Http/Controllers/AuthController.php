<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Handlers\AuthHandler;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $handler;

    public function __construct(AuthHandler $handler)
    {
        $this->handler = $handler;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {

            $result = $this->handler->handleRegister($data);

            DB::commit();
            return responseHelper::success($result, trans('alert.register_success'), Response::HTTP_CREATED);

        } catch (\Throwable $th) {  
            DB::rollback();
            return ResponseHelper::error(message: trans('alert.register_failed') . " => " .$th->getMessage());
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $result = $this->handler->handlerVerifyOtp($data['user_id'], $data['otp']);

            DB::commit();
            return ResponseHelper::success($result, trans('alert.verify_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: $th->getMessage(), code: $th->getCode() ?: Response::HTTP_BAD_REQUEST);
        }
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $result = $this->handler->handleResendOtp($data['user_id']);

            DB::commit();
            return ResponseHelper::success($result, trans('alert.otp_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    public function login(LoginRequest $request) 
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $result = $this->handler->handleLogin($data['email'], $data['password']);

            DB::commit();
            return ResponseHelper::success($result, trans('alert.login_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = $th->getCode() === 403 ? Response::HTTP_FORBIDDEN : Response::HTTP_UNAUTHORIZED;
            return ResponseHelper::error(message: $th->getMessage(), code: $code);
        }
    }


    public function me(Request $request)
    {
        try {
            return ResponseHelper::success($request->user(), trans('alert.get_current_user'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }


    public function logout(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->user()->currentAccessToken()->delete();

            DB::commit();
            return ResponseHelper::success(message: trans('alert.logout_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: trans('auth.logout_error') . '=>' . $th->getMessage());
        }
    }
}
