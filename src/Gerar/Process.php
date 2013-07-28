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

    public static function runAndCheckReturnCode($command)
    {
        $output = '';
        $retVal = null;
        exec($command, $output, $retVal);

        if($retVal != 0) {
            Console::log("While running '$command' an error happened:");
            Console::log($output);
            throw new \RuntimeException();
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
