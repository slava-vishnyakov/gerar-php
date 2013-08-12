<?php

namespace Gerar;

global $argv;

require __DIR__ . '/../vendor/autoload.php';


try {
    if (!empty($argv[1])) {
        require $argv[1];
    } elseif ($script = file_get_contents('php://stdin')) {
        eval('?>' . $script);
    } elseif ($argv[1] == '--set-hostname') {
        Hostname::change($argv[2]);
    } else {
        print "-- Gerar PHP --                      \n";
        print "                                     \n";
        print "Usage:                               \n";
        print "  cat file.php | gerar               \n";
        print "  gerar --set-hostname hostname      \n";
        print "  gerar file.php                     \n";
        exit();
    }
} catch (Exception $e) {
    print "---------------------------------------------\n";
    print "[EXCEPTION] " . $e->getMessage() . "\n";
    print $e->getTraceAsString();
    print "---------------------------------------------\n";
}