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

    public static function mainExternalIp()
    {
        if(ThisServer::isLinux()) {
            return Process::read("ifconfig  | grep 'inet addr:'| grep -v '127.0.0.1' | cut -d: -f2 | awk '{ print \$1}'");
        }
        Gerar::notImplemented();
    }


}
