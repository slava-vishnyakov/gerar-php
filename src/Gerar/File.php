<?php

namespace Gerar;

class File
{
    function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param string $fileName
     *
     * @return File
     */
    public static function named($fileName)
    {
        return new File($fileName);
    }

    /**
     * @return string
     */
    public function read()
    {
        return file_get_contents($this->fileName);
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->fileName);
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function write($content)
    {
        file_put_contents($this->fileName, $content);

        return $this;
    }

    /**
     * @param mixed $callable
     *
     * @return $this
     */
    public function whenChanges($callable)
    {
        $cacheFile = Gerar::getCacheFile("md5-" . md5($this->fileName));
        $md5       = md5_file($this->fileName);

        if ((!$cacheFile->exists()) || ($cacheFile->read() != $md5)) {
            call_user_func($callable);
            $md5 = md5_file($this->fileName);
            $cacheFile->write($md5);
        }

        return $this;
    }

    /**
     * @param string $needle
     * @param string $newNeedle
     *
     * @return $this
     */
    public function replaceIfPresent($needle, $newNeedle)
    {
        if ($this->contains($needle)) {
            $content = $this->read();
            if ($needle instanceof RegExp) {
                $newContent = preg_replace($needle->regexp, $newNeedle, $content);
            } else {
                $newContent = str_replace($needle, $newNeedle, $content);
            }
            $this->write($newContent);
        }

        return $this;
    }

    /**
     * @param string $needle
     *
     * @return integer|string
     */
    public function contains($needle)
    {
        if ($needle instanceof RegExp) {
            return preg_match($needle->regexp, $this->read());
        } else {
            return strstr($this->read(), $needle);
        }
    }

    /**
     * @param string $newContent
     */
    public function append($newContent)
    {
        $this->write($this->read() . $newContent);
    }

    /**
     * @param string $string
     */
    public function chmod($string)
    {
        chmod($this->fileName, $string);
    }

    /**
     * @param string $needle
     *
     * @return mixed
     */
    public function findString($needle)
    {
        if ($needle instanceof RegExp) {
            preg_match_all($needle->regexp, $this->read(), $m);
            if (isset($m[1])) {
                return $m[1];
            }
            if (isset($m[0])) {
                return $m[0];
            }
        } else {
            if (strstr($this->read(), $needle)) {
                return $needle;
            };
        }

        return null;
    }

    /**
     * @param string $lines
     */
    public function shouldHaveLines($lines)
    {
        if (!$this->contains($lines)) {
            $this->append($lines);
        }
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function shouldHaveLine($string)
    {
        if (!($string instanceof RegExp)) {
            $searchString = new RegExp("/^" . rtrim(str_replace('/', '\\/', preg_quote($string))) . "$/m");
        } else {
            $searchString = $string;
        }

        if (substr($searchString, -1, 1) != "\n") {
            $searchString .= "\n";
        }

        if (!$this->findString($searchString)) {
            $this->append($string . "\n");
        }

        return $this;
    }

}