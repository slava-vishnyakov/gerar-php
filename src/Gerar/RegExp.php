<?php

namespace Gerar;

class RegExp
{
    /**
     * @var string
     */
    public $regexp;

    function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return "RegExp({$this->regexp})";
    }


}