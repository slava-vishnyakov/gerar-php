<?php

namespace Gerar;

class DoOnce {
    public static function run($name, $callable)
    {
        $file = File::named(Gerar::getCacheFileName(".once-$name"));
        if(!$file->exists()) {
            call_user_func($callable);
            $file->write("");
        }
    }
}