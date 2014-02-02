<?php

namespace Gerar;

class HttpResponse
{
    /**
     * @var string
     */
    public $body;

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}

class Http
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $onSuccess;

    function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $url
     *
     * @return Http
     */
    public static function request($url)
    {
        return new Http($url);
    }

    /**
     * @param string $callback
     *
     * @return $this
     */
    public function onSuccess($callback)
    {
        $this->onSuccess = $callback;

        return $this;
    }

    /**
     * @return $this
     */
    public function run()
    {
        $response       = new HttpResponse;
        $response->body = file_get_contents($this->url);
        if ($this->onSuccess) {
            call_user_func_array($this->onSuccess, array(null, $response));
        }

        return $this;
    }
}