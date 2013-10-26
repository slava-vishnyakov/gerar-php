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
            throw new Exception("Command $command failed");
        }
        return join("\n", $output);
    }

    public static function runInBash($arg)
    {
        return self::read("bash -lc " . escapeshellarg($arg));
    }

    public static function runAndCheckReturnCode($command)
    {
        $output = '';
        $retVal = null;
        exec($command, $output, $retVal);

        $output = join("\n", $output);

        if($retVal != 0) {
            Console::log("While running '$command' an error happened:");
            Console::log($output);
            throw new Exception();
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

    public static function runInBashAs($user, $command)
    {
        return self::read("sudo -i -u " . escapeshellarg($user) . " bash -lc " . escapeshellarg($command));
    }
}