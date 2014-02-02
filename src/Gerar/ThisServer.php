<?php

namespace Gerar;

class ThisServer
{
    /**
     * @return string
     */
    public static function hostname()
    {
        return trim(Process::read('hostname'));
    }

    /**
     * @return boolean
     */
    public static function isMac()
    {
        return PHP_OS == 'Darwin';
    }

    /**
     * @return boolean
     */
    public static function isDebian()
    {
        if (self::isLinux()) {
            if (Process::read("cat /etc/debian_release | grep ID") == "ID=debian") {
                return true;
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isUbuntu()
    {
        if (self::isLinux()) {
            if (Process::read("cat /etc/lsb-release | grep _ID") == "DISTRIB_ID=Ubuntu") {
                return true;
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isLinux()
    {
        return PHP_OS == 'Linux';
    }

    /**
     * @return string
     */
    public static function mainExternalIp()
    {
        if (self::isLinux()) {
            return Process::read(
                "ifconfig  | grep 'inet addr:'| grep -v '127.0.0.1' | cut -d: -f2 | awk '{ print \$1}'"
            );
        }
        Gerar::notImplemented();
    }


}
