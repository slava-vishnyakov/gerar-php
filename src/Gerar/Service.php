<?php

namespace Gerar;

class Service
{
    function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     *
     * @return Service
     */
    public static function named($name)
    {
        return new self($name);
    }

    /**
     * @return $this
     */
    public function shouldBeRunning()
    {
        if (ThisServer::isUbuntu() || ThisServer::isDebian()) {
            try {
                $status = Process::read("service {$this->name} status 2> /dev/null");
            } catch (Exception $e) {
                $status = '';
            }
            if ($status != 'running') {
                $this->start();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function shouldBeInstalled()
    {
        Package::named($this->name)->shouldBeInstalled();

        return $this;
    }

    /**
     * @return boolean
     */
    public function shouldBeRunningAtReboots()
    {
        if (ThisServer::isUbuntu() || ThisServer::isDebian()) {
            return Process::runAndCheckReturnCode("update-rc.d {$this->name} defaults");
        }
        Gerar::notImplemented();
    }

    public function stop()
    {
        Console::log("Stoping {$this->name} service");
        Process::runAndCheckReturnCode("service {$this->name} stop");
    }

    public function start()
    {
        Console::log("Starting {$this->name} service");
        Process::runAndCheckReturnCode("service {$this->name} start");
    }

    public function reload()
    {
        Console::log("Reloading {$this->name} service");
        Process::runAndCheckReturnCode("service {$this->name} reload");
    }

    public function forceReload()
    {
        Console::log("Force-reloading {$this->name} service");
        Process::runAndCheckReturnCode("service {$this->name} force-reload");
    }

    public function restart()
    {
        Console::log("Restarting {$this->name} service");
        Process::runAndCheckReturnCode("service {$this->name} restart");
    }

    /**
     * @return $this
     */
    public function shouldBeRemoved()
    {
        Package::named($this->name)->shouldBeRemoved();

        return $this;
    }
}