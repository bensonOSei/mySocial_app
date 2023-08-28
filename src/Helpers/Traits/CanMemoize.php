<?php

namespace Benson\InforSharing\Helpers\Traits;

trait CanMemoize
{
    private static array $cache = [];

    /**
     * Memoize a callback
     */
    public function memoize($key, $callback)
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }
        static::$cache[$key] = $callback();
        return static::$cache[$key];
    }


}


// use memoize

