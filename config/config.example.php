<?php

/**
 * @see http://wiki.kbra.network/wiki/display/DEV/Redis
 */
return [
    'driver' => 'redis',
    'maxRetries' => 3,
    'config' => [
        'defaultTtl' => 900,
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0,
        'password' => '',
        'timeout' => 3,
    ],
];
