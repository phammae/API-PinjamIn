<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class OtpService
{
    protected $twilio;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        if ($sid && $token) {
            $this->twilio = new Client($sid, $token);
        }
    }

    // Generate OTP
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // OTP expire 
    public function getExpirationTime(): Carbon 
    {
        return Carbon::now()->addMinute(5);    
    }

    // send otp via SMS
    public function sendOtp(string $phone, string $otp): bool 
    {
        try {
            if (!$this->twilio) {
                Log::info("OTP for {$phone}: {$otp}");
                return true;
            }

            $this->twilio->messages->create(
                $phone,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => "Your OTP code is: {$otp}. Valid for 5 minutes."
                ]
            );

            return true;
        } catch (\Exception $e) {
            Log::errpr("Failed send OTP: " .  $e->getMessage());
            throw new \Exception("Failed to send OTP");
        }    
    }

    // Format phone number
    public function formatPhoneNumber(string $phone): string 
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);  
        
        if (substr($phone, 0, 1) == '0') {
            $phone = '+62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '+62' . $phone;
        } else {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    // check OTP expired

    public function isExpired($expiresAt): bool 
    {
        return Carbon::now()->isAfter($expiresAt);   
    }
}