<?php

namespace Gerar;

class PackageList
{
    public function __construct(array $names)
    {
        $this->names = $names;
    }

    /**
     * @param string $function
     * @param array  $arguments
     */
    public function __call($function, $arguments)
    {
        foreach ($this->names as $name) {
            $package = new Package($name);
            call_user_func_array(array($package, $function), $arguments);
        }
    }
}