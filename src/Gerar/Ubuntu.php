<?php

namespace Gerar;

class Ubuntu {
    public static function fixLocales()
    {
        if(ThisServer::isUbuntu()) {
            if(!File::named('/etc/environment')->contains('LC_ALL=en_US.UTF-8')) {
                Console::log("Fixing locale files");
                File::named('/etc/environment')
                    ->shouldHaveLine('LC_ALL=en_US.UTF-8')
                    ->shouldHaveLine('LANG=en_US.UTF-8');
                Process::runInBash('locale-gen en_US en_US.UTF-8');
                Process::runInBash('dpkg-reconfigure locales');
            }
        }
    }

    public static function update()
    {
        Console::log("apt-get update");
        Process::runAndCheckReturnCode("apt-get update");
    }
}