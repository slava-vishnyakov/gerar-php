<?php

namespace Gerar;

require __DIR__ . '/vendor/autoload.php';

if($argv[1] == '--set-hostname') {
    Hostname::change($argv[2]);
} else {
    require $argv[1];
}