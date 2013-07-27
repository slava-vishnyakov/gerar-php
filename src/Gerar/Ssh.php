<?php

namespace Gerar;

class Ssh
{
    public static function server()
    {
        return new self;
    }

    public function securify()
    {
        User::shouldHaveOneSudoUserWithSshKey();
        $this->shouldNotAllowRoot();
        $this->shouldNotAllowPlainTextPasswords();
    }

    private function shouldNotAllowRoot()
    {
        File::named('/etc/ssh/sshd_config')
            ->replaceIfPresent(new RegExp("/^#?PermitRootLogin yes/m"), 'PermitRootLogin no')
            ->shouldHaveLine("PermitRootLogin no");
    }

    private function shouldNotAllowPlainTextPasswords()
    {
        File::named('/etc/ssh/sshd_config')
            ->replaceIfPresent(new RegExp("/^#?PasswordAuthentication yes/m"), 'PasswordAuthentication no')
            ->shouldHaveLine("PasswordAuthentication no")
            ->whenChanges(function() {
                Service::named('ssh')->restart();
            });
    }

}