<?php

namespace Gerar;

class EtcHosts
{
    public static function file()
    {
        return new self;
    }

    public function shouldResolve($host, $ip)
    {
        if (ThisServer::isLinux()) {
            $line = "$ip $host\n";
            if ((@gethostbyaddr($host) != $ip) && (!$this->actualFile()->contains($line))) {
                Console::log("Resoving $host -> $ip (via /etc/hosts)");
                $this->actualFile()->shouldHaveLine($line);
            }
            return $this;
        }
        Gerar::notImplemented();
    }

    public function shouldNotResolve($host)
    {
        if (ThisServer::isLinux()) {
            $this->actualFile()
                # TODO: This does not work
                ->replaceIfPresent(new RegExp('/^\s*(\d\.)+\s+\b' . preg_quote($host) . '\s*$/m'), '')
                ->replaceIfPresent(new RegExp('/^(.*?)\b' . preg_quote($host) . '\b/m'), '\1 # removed');
            return $this;
        }
        Gerar::notImplemented();
    }

    /**
     * @return File
     */
    public function actualFile()
    {
        return File::named("/etc/hosts");
    }


}