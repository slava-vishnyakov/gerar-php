<?php

namespace Gerar;

class Ubuntu {
    public static function fixLocales()
    {
        if(ThisServer::isUbuntu()) {
            if(!File::named('/etc/environment')->contains('LC_ALL=en_US.UTF-8')) {
                Console::log("Fixing locale files");
                Process::runAndCheckReturnCode('\
                    echo "LC_ALL=en_US.UTF-8" >> /etc/environment;\
                    echo "LANG=en_US.UTF-8" >> /etc/environment;\
                    locale-gen en_US en_US.UTF-8 && dpkg-reconfigure locales\
                ');
            }
        }
    }

    public static function update()
    {
        Console::log("apt-get update");
        Process::runAndCheckReturnCode("apt-get update");
    }
}