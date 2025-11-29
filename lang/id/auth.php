<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'invalid_api_token' => 'Api token tidak valid',
    'logout_success' => 'Berhasil logout',
    'logout_error' => 'Terjadi kesalahan! gagal logout, coba lagi nanti',
    'failed' => 'Email/Nomor telepon atau Password salah',
    'invalid_bearer_token' => 'Sesi Login tidak valid atau telah berakhir',
    'invalid_authorization' => 'Role user tidak valid',
    'login_success' => 'Berhasil login',
    
    // Registration & OTP
    'register_success' => 'Berhasil register, OTP telah dikirim ke nomor telepon Anda',
    'register_failed' => 'Gagal melakukan registrasi',
    'verify_success' => 'Verifikasi OTP berhasil',
    'verify_failed' => 'Verifikasi OTP gagal',
    'resend_otp_success' => 'OTP berhasil dikirim ulang',
    'resend_otp_failed' => 'Gagal mengirim ulang OTP',
    
    // OTP Validation
    'user_not_found' => 'User tidak ditemukan',
    'already_verified' => 'User sudah terverifikasi',
    'otp_expired' => 'Kode OTP sudah kadaluarsa',
    'invalid_otp' => 'Kode OTP tidak valid',
    'not_verified' => 'Akun Anda belum terverifikasi, silakan verifikasi dengan OTP terlebih dahulu',
];
