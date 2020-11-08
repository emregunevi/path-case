<?php


namespace App\Service;


class AuthorizationService
{

    function encrypt($data, $key) {

        $encryption_key = base64_decode($key);

        $secret_iv = base64_decode($this->generateRandomString(20));

        $iv = substr(hash('sha256', $secret_iv), 0, openssl_cipher_iv_length('aes-256-cbc'));

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);

        return base64_encode($encrypted . '::' . $iv);
    }

    function decrypt($data, $key) {

        $encryption_key = base64_decode($key);

        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);

        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    function encryptAccessToken($data, $key) {

        $encryption_key = base64_decode($key);

        $secret_iv = base64_decode($this->generateRandomString(10));

        $iv = substr(hash('sha256', $secret_iv), 0, openssl_cipher_iv_length('aes-256-cbc'));

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);

        return base64_encode($encrypted . '::' . $iv);
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}