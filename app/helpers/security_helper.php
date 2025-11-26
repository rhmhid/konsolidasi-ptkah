<?php if (!defined("BASEPATH")) exit("No direct script access allowed");

    function security () /*{{{*/
    {
        $secret = array(
            'secret_key'        => getenv('SECRET_KEY'),
            'secret_iv'         => getenv('SECRET_IV'),
            'encrypt_method'    => getenv('ENC_METHOD'),
        );

        return $secret;
    } /*}}}*/

    function encrypt ($string) /*{{{*/
    {
        $output = false;

        $secret         = security();
        $secret_key     = $secret["secret_key"];
        $secret_iv      = $secret["secret_iv"];
        $encrypt_method = $secret["encrypt_method"];

        // hash
        $key    = hash("sha256", $secret_key);

        // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
        $iv     = substr(hash("sha256", $secret_iv), 0, 16);

        // do the encryption given text/string/number
        $result = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($result);

        return $output;
    } /*}}}*/

    function decrypt ($string) /*{{{*/
    {
        $output = false;

        $secret         = security();
        $secret_key     = $secret["secret_key"];
        $secret_iv      = $secret["secret_iv"];
        $encrypt_method = $secret["encrypt_method"];

        // hash
        $key    = hash("sha256", $secret_key);

        // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
        $iv     = substr(hash("sha256", $secret_iv), 0, 16);

        // do the decryption given text/string/number
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

        return $output;
    } /*}}}*/

    function CheckPassword ($userpass, $userpass_hash) /*{{{*/
    {
        $pass_encrypt = encrypt($userpass);

        $result = password_verify($pass_encrypt, $userpass_hash) ? true : false;

        return $result;
    } /*}}}*/

    function CreatePassword ($userpass) /*{{{*/
    {
        $pass_encrypt = encrypt($userpass);

        $pass_hash = password_hash($pass_encrypt, PASSWORD_DEFAULT);

        return $pass_hash;
    } /*}}}*/
?>