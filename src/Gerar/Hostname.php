<?php

namespace Gerar;

class Hostname
{
    public static function run($hostname, $function)
    {
        if (ThisServer::hostname() == $hostname) {
            call_user_func($function);
        }
    }
}
