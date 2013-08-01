<?php

namespace Gerar;

class Port {
    public $number;

    function __construct($number)
    {
        $this->number = $number;
    }

    public static function number($number)
    {
        return new Port($number);
    }

    public function ifNotResponding($callable)
    {
        if(!$this->isResponding()) {
            call_user_func($callable);
        }
    }

    private function isResponding()
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new Exception("Cannot create socket");
        }

        $result = @socket_connect($socket, '127.0.0.1', $this->number);
        return $result !== false;
    }
}