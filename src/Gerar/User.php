<?php

namespace Gerar;

class User
{
    function __construct($name)
    {
        $this->name = $name;
    }

    public static function named($name)
    {
        return new User($name);
    }

    public function getHome()
    {
        return trim(Process::read("sudo -u {$this->name} echo \$HOME"));
    }

    public function shouldHaveSshKey()
    {
        if (ThisServer::isUbuntu()) {
            $key = $this->getPrivateKeyFilename();
            if(!File::named($key)->exists()) {
                Process::runAndCheckReturnCode("sudo -u {$this->name} ssh-keygen -t rsa -N '' -f " . escapeshellarg($key));
            }
            return $this;
        }
        Gerar::notImplemented();
    }

    public function mailPublicKeyOnce($to)
    {
        DoOnce::run("mail-ssh-key-{$this->name}", function() use($to) {
            Mail::sendSimple($to, "Public Key of {$this->name}", $this->getPublicKey());
        });
    }

    public function getPrivateKeyFilename()
    {
        return "{$this->getHome()}/.ssh/id_rsa";
    }

    public function getPublicKeyFilename()
    {
        return "{$this->getHome()}/.ssh/id_rsa.pub";
    }

    private function getPublicKey()
    {
        return File::named($this->getPublicKeyFilename())->read();
    }

    public function shouldBePresent()
    {
        if(ThisServer::isLinux()) {
            if(!$this->exists()) {
                $this->create();
            }
        }
        return $this;
    }

    public function exists()
    {
        return Process::getReturnCode("id {$this->name} >/dev/null 2>&1") == 0;
    }

    public function create()
    {
        Process::runAndCheckReturnCode("useradd {$this->name} -m");
    }

    public function haveSshKeyFrom($filename)
    {
        $content = trim(File::named($filename)->read());
        $file = $this->authorizedKeysFile();
        if(!$file->exists()) {
            $file->write($content);
            $file->chmod(0600);
        }

        if(!$file->contains($content)) {
            $file->append($content . "\n");
        }

        return $this;
    }

    private function authorizedKeysFile()
    {
        return File::named($this->getHome() . '/.ssh/authorized_keys');
    }

    public function shouldBeInGroup($groupName)
    {
        if(!$this->inGroup($groupName)) {
            $this->addToGroup($groupName);
        }
        return $this;
    }

    public function inGroup($groupName)
    {
        $groups = $this->getGroups();
        return in_array($groupName, $groups);
    }

    public function getGroups()
    {
        return explode(' ', Process::read("id -Gn {$this->name}"));
    }

    private function addToGroup($groupName)
    {
        Process::runAndCheckReturnCode("usermod -aG {$groupName} {$this->name}");
    }

    public function shouldHaveNoPasswordSudoFor($commands)
    {
        if(is_string($commands)) {
            $commands = array($commands);
        }

        $sudoers = File::named('/etc/sudoers');
        # gatoatigrado ALL=NOPASSWD: /bin/set-slow-cpufreq

        $test = "test\nfsfsddfs\ndfsfsddsfds";

        $line = $sudoers->findString(new RegExp("/^{$this->name}\\s.*?/m"));
        print $line;
    }
}