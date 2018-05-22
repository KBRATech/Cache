<?php

namespace Kbra\Cache;

use Exception;

class CacheServiceException extends Exception
{
    /**
     * @param string $cacheDriver
     * @param int $numberOfTries
     * @param throwable $previousException
     * @return CacheServiceException
     */
    public static function connectionFailed($cacheDriver, $numberOfTries, $previousException = null) {
        $message = sprintf("Could not connect to %s after %d tries", $cacheDriver, $numberOfTries);

        return new static($message, 666, $previousException);
    }
}
