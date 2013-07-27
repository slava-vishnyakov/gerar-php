<?php

namespace Gerar;

class HttpResponse
{
    public $body;

    public function getBody()
    {
        return $this->body;
    }
}

class Http {
    public $url;
    public $onSuccess;

    function __construct($url)
    {
        $this->url = $url;
    }

    public static function request($url)
    {
        return new Http($url);
    }

    public function onSuccess($callback)
    {
        $this->onSuccess = $callback;
        return $this;
    }

    public function run()
    {
        $response = new HttpResponse;
        $response->body = file_get_contents($this->url);
        if($this->onSuccess) {
            call_user_func_array($this->onSuccess, array(null, $response));
        }
        return $this;
    }
}