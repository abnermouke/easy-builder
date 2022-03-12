<?php

use Abnermouke\LaravelBuilder\Library\Cryptography\DecryptionLibrary;
use Abnermouke\LaravelBuilder\Library\Cryptography\EncryptionLibrary;

//初始化加密KEY与SECRET
$app_key = 'pDOvn7hOJp7Q4K1R5xSFBQP4VBDVNuax';
$app_secret = '9RPi8X0zFXePjJZdv7t2xfUvqsbkv7T1';
//配置需要加密内容
$encrypt_data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => ['e' => 5, 'f' => 'abcdefg']];
//加密内容（本地私钥、外部公钥加密）
$encrypt_content = (new EncryptionLibrary(
    $app_key,
    $app_secret,
    file_get_contents(base_path('packages/abnermouke/laravel-builder/data/rsa/inside/private_pkcs8.key')),
    file_get_contents(base_path('packages/abnermouke/laravel-builder/data/rsa/inside/private_pkcs1.key')),
    file_get_contents(base_path('packages/abnermouke/laravel-builder/data/rsa/outside/public.key'))
))->encrypt($encrypt_data);
//解密内容（本地私钥、外部公钥解密）
$decrypt_data = (new DecryptionLibrary(
    $app_key,
    $app_secret,
    file_get_contents(base_path('packages/abnermouke/laravel-builder/data/rsa/outside/private_pkcs8.key')),
    file_get_contents(base_path('packages/abnermouke/laravel-builder/data/rsa/outside/private_pkcs1.key')),
    file_get_contents(base_path('packages/abnermouke/laravel-builder/data/rsa/inside/public.key'))
))->decrypt($encrypt_content);
