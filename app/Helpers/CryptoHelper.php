<?php

namespace App\Helpers;

class CryptoHelper
{
    private static $method = "AES-256-CBC";
    private static $secret_key = "craftstroke_secret_key";
    private static $secret_iv = "craftstroke_secret_iv";

    private static function getKey()
    {
        return hash('sha256', self::$secret_key);
    }

    private static function getIV()
    { return substr(hash('sha256', self::$secret_iv), 0, 16);}

    public static function encrypt($data)
    {
        $output = openssl_encrypt(
            $data,
            self::$method,
            self::getKey(),
            0,
            self::getIV()
        );

        return base64_encode($output);
    }

    public static function decrypt($data)
    {
        $output = openssl_decrypt(
            base64_decode($data),
            self::$method,
            self::getKey(),
            0,
            self::getIV()
        );

        return $output;
    }
}