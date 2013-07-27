<?php

namespace Gerar;

class Console
{

    public static function log($message)
    {
        file_put_contents("php://stderr", "[Console] " . $message . "\n");
    }
}
