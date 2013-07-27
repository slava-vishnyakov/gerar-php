<?php

namespace Gerar;

class Service {
    function __construct($name)
    {
        $this->name = $name;
    }

    public static function named($name)
    {
        return new Service($name);
    }

    public function shouldBeRunning()
    {
        if(ThisServer::isUbuntu()) {
            $status = Process::read("service {$this->name} status 2> /dev/null");
            if($status != 'running') {
                $this->start();
            }
        }
        return $this;
    }

    public function shouldBeInstalled()
    {
        Package::named($this->name)->shouldBeInstalled();
        return $this;
    }

    public function shouldBeRunningAtReboots()
    {
        if(ThisServer::isUbuntu()){
            return Process::runAndCheckReturnCode("update-rc.d {$this->name} defaults");
        }
        Gerar::notImplemented();
    }

    public function start()
    {
        Process::runAndCheckReturnCode("service {$this->name} start");
    }
}