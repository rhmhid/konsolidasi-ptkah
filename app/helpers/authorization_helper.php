<?php

class AUTHORIZATION
{
    public static function validateTimestamp($token)
    {
        $token = self::validateToken($token);

        if ($token != false && (now() - $token->timestamp < (config_item('token_timeout') * 60)))
        {
            return $token;
        }

        return false;
    }

    public static function validateToken($token)
    {
        return JWT::decode($token, config_item('jwt_key'));
    }

    public static function generateToken($data)
    {
        return JWT::encode($data, config_item('jwt_key'));
    }
}