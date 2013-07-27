<?php

namespace Gerar;

class ThisServer
{
    public static function hostname()
    {
        return trim(Process::read('hostname'));
    }

    public static function isMac()
    {
        return PHP_OS == 'Darwin';
    }

    public static function isUbuntu()
    {
        if(ThisServer::isLinux()) {
            if(Process::read("cat /etc/lsb-release | grep _ID") == "DISTRIB_ID=Ubuntu") {
                return true;
            }
        }
        return false;
    }

    public static function isLinux()
    {
        return PHP_OS == 'Linux';
    }


}
