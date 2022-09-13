<?php

return [
    'consumer'  => [
        'handler'     => Wenstudio\BeanstalkQueue\Process\Consumer::class,
        'count'       => 1, // could be more than 1
        'constructor' => [
            // consumer subdir
            'consumer_dir' => app_path() . '/queue/beanstalk'
        ]
    ]
];
