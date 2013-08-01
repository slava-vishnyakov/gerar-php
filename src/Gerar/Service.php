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
            try {
                $status = Process::read("service {$this->name} status 2> /dev/null");
            } catch (Exception $e) {
                $status = '';
            }
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
        Console::log("Starting {$this->name} service");
        Process::runAndCheckReturnCode("service {$this->name} start");
    }

    public function restart()
    {
        Console::log("Restarting {$this->name} service");
        Process::runAndCheckReturnCode("service {$this->name} restart");
    }

    public function shouldBeRemoved()
    {
        Package::named($this->name)->shouldBeRemoved();
        return $this;

    }
}