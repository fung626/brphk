<?php

namespace App\Mylibs;

define('CRYPT_RSA_PKCS15_COMPAT', true);

use phpseclib\Crypt\AES;
use phpseclib\Crypt\RSA;

class Encryptor
{

    public static function createKeyPair($size = 1024)
    {
        $rsa = new RSA();
        return $rsa->createKey($size);
    }

    public static function sign($key, $data)
    {
        // dd($data);
        $data = md5($data);
        return self::RSAEncrypt($key, $data);
    }

    public static function RSAEncrypt($key, $data)
    {
        // $data = hex2bin($data);
        $rsa = new RSA();
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        $rsa->loadKey(self::parseKey($key)); // private key
        return $rsa->encrypt($data);
    }

    public static function RSADecrypt($key, $data)
    {
        $data = hex2bin($data);
        // dd($data);
        $rsa = new RSA();
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        $rsa->loadKey(self::parseKey($key)); // private key
        return $rsa->decrypt($data);
    }

    public static function AESEncrypt($key, $data)
    {
        // $data = hex2bin($data);
        $aes = new AES(AES::MODE_ECB);
        $aes->setKey($key);
        return $aes->encrypt($data);
    }

    public static function AESDecrypt($key, $data)
    {
        // dd('y');
        $data = hex2bin($data);
        $aes = new AES(AES::MODE_ECB);
        $aes->setKey($key);
        return $aes->decrypt($data);
    }

    public static function parseKey($key)
    {
        return trim(preg_replace('/\s+/', '', $key));
    }

}