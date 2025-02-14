<?php

namespace App\Utils;

class Console {
    public static function write($message) {
        echo "\033[32m$message\033[0m\n"; // Texte en vert
    }

    public static function ask($message) {
        echo "\033[33m$message: \033[0m";
        return trim(fgets(STDIN));
    }
}
