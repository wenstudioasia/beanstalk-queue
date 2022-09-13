<?php

return [
    'default' => [
        'ip' => '127.0.0.1',
        'port' => 11300,
        'timeout' => 10, // s
        'options' => [
            'auth' => '123456', // password
            'delay'  => 2,      // delay seconds
            'retry_after' => 5, // in seconds
        ]
    ],
];