<?php

namespace App\Core;

class Request
{
    public function getBody()
    {
        return $_POST;
    }
}
