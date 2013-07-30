<?php

namespace Gerar;

require __DIR__ . '/../vendor/autoload.php';

if(empty($argv[1])) {
    print "-- Gerar PHP --";
    print "Usage: ";
    print "  gerar --set-hostname hostname";
    print "  gerar file.php";
    exit();
}

if($argv[1] == '--set-hostname') {
    Hostname::change($argv[2]);
} else {
    require $argv[1];
}