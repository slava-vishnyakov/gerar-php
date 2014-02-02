<?php

namespace Gerar;

class Port
{
    /**
     * @var integer
     */
    public $number;

    function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * @param integer $number
     *
     * @return Port
     */
    public static function number($number)
    {
        return new self($number);
    }

    /**
     * @param string $callable
     */
    public function ifNotResponding($callable)
    {
        if (!$this->isResponding()) {
            call_user_func($callable);
        }
    }

    /**
     * @return boolean
     * @throws Exception
     */
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