<?php

namespace Gerar;

class Mail
{
    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     *
     * @throws Exception
     */
    public function sendSimple($to, $subject, $body)
    {
        if (!strstr(ThisServer::hostname(), '.')) {
            Console::log("Local hostname (" . ThisServer::hostname() . ") should contain .");
            throw new Exception();
        }
        Package::named('postfix sendmail')->shouldBeInstalled();
        mail($to, $subject, $body);
    }
}