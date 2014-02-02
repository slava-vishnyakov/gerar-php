<?php

namespace Gerar;

class Gerar
{
    /**
     * @throws Exception
     */
    public static function notImplemented()
    {
        throw new Exception("Not implemented");
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function getCacheFileName($name)
    {
        $cacheDir = User::named('root')->getHome() . "/.gerar/cache/";
        if (!File::named($cacheDir)->exists()) {
            mkdir($cacheDir, 0600, true);
        }

        return $cacheDir . $name;
    }

    /**
     * @param string $name
     *
     * @return File
     */
    public static function getCacheFile($name)
    {
        return File::named(self::getCacheFileName($name));
    }
}