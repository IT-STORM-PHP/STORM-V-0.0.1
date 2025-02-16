<?php

namespace App\Middleware;

class SessionMiddleware
{
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
}
