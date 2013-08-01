<?php

namespace Gerar;

class Gerar {

    public static function notImplemented()
    {
        throw new Exception("Not implemented");
    }

    public static function getCacheFileName($name)
    {
        $cacheDir = User::named('root')->getHome() . "/.gerar/cache/";
        if(!File::named($cacheDir)->exists()) {
            mkdir($cacheDir, 0600, true);
        }
        return $cacheDir . $name;
    }

    public static function getCacheFile($name)
    {
        return File::named(self::getCacheFileName($name));
    }
}