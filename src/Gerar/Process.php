<?php

namespace Gerar;

class Process
{
    public static function read($command)
    {
        $output = '';
        $retVal = null;
        exec($command, $output, $retVal);
        if($retVal != 0) {
            throw new \RuntimeException("Command $command failed");
        }
        return join("\n", $output);
    }

    public static function runAndCheckReturnCode($string)
    {
        $result = Process::read("($string  >/dev/null 2>/tmp/error.txt) && echo 'YES'");
        if($result != "YES") {
            $error = file_get_contents('/tmp/error.txt');
            Console::log("While running '$string' an error happened:");
            Console::log($error);
            throw new \RuntimeException($error);
        }
        return true;
    }

    public static function getReturnCode($command)
    {
        $output = '';
        $retVal = null;
        exec($command, $output, $retVal);
        return $retVal;
    }
}
