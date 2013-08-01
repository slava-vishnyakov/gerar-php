<?php

namespace Gerar;

class Mail {
    function sendSimple($to, $subject, $body) {
        if(!strstr(ThisServer::hostname(), '.')) {
            Console::log("Local hostname (".ThisServer::hostname().") should contain .");
            throw new Exception();
        }
        Package::named('postfix sendmail')->shouldBeInstalled();
        mail($to, $subject, $body);
    }
}