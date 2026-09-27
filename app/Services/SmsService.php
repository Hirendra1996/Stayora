<?php
namespace App\Services;

class SmsService {

    private string $driver;
    private $service;

    public function __construct(?string $driver = null) {
        $config = require __DIR__ . '/../Config/sms.php';
        $this->driver = strtolower($driver ?: ($config['default'] ?? 'msg91'));

        if ($this->driver === 'twilio') {
            $this->service = new TwilioService();
        } else {
            $this->driver = 'msg91';
            $this->service = new Msg91Service();
        }
    }

    /**
     * Get the active SMS driver name ('msg91' or 'twilio')
     */
    public function getActiveDriver(): string {
        return $this->driver;
    }

    /**
     * Check if active driver is configured
     */
    public function isConfigured(): bool {
        return $this->service->isConfigured();
    }

    /**
     * Send OTP SMS via the active SMS provider
     *
     * @param string $phone
     * @param string $otp
     * @param string $purpose
     * @return array
     */
    public function sendOtpSms(string $phone, string $otp, string $purpose = 'verification'): array {
        return $this->service->sendOtpSms($phone, $otp, $purpose);
    }
}
