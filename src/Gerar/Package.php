<?php

namespace Gerar;

class Package
{
    function __construct($name)
    {
        $this->name = $name;
    }

    public static function named($name)
    {
        if(strstr($name, ' ')) {
            return new PackageList(explode(' ', $name));
        } else {
            return new Package($name);
        }
    }

    public function shouldBeInstalled()
    {
        if(!$this->isInstalled()) {
            $this->install();
        }
    }

    public function shouldBeRemoved()
    {
        if($this->isInstalled()) {
            $this->remove();
        }
    }

    public function isInstalled()
    {
        if(ThisServer::isUbuntu()) {
            return Process::getReturnCode("dpkg -s '{$this->name}' 2>/dev/null >/dev/null") == 0;
        }
        Gerar::notImplemented();
    }

    private function install()
    {
        Console::log("Package {$this->name} will be installed");
        if(ThisServer::isUbuntu()) {
            Process::runAndCheckReturnCode("DEBIAN_FRONTEND=noninteractive apt-get install -y {$this->name}");
            return $this;
        }
        Gerar::notImplemented();
    }

    private function remove()
    {
        Console::log("Package {$this->name} will be removed");
        if(ThisServer::isUbuntu()) {
            Process::runAndCheckReturnCode("DEBIAN_FRONTEND=noninteractive apt-get remove -y {$this->name}");
            return $this;
        }
        Gerar::notImplemented();
    }
}
