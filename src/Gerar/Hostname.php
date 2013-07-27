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

    public static function change($string)
    {
        if(ThisServer::hostname() != $string) {
            File::named('/etc/hosts')
                ->shouldHaveLine("127.0.0.1 $string\n");

            File::named('/etc/host')
                ->write("$string");

            Process::runAndCheckReturnCode("hostname $string");
        }
    }
}
