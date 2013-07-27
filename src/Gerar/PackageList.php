<?php

namespace Gerar;

class PackageList
{
    function __construct(array $names)
    {
        $this->names = $names;
    }

    public function __call($function, $arguments)
    {
        foreach($this->names as $name) {
            $package = new Package($name);
            call_user_func_array(array($package, $function), $arguments);
        }
    }
}