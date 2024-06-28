<?php

return [
    'common' => [
        'fail' => [
            'server' => [
                'error' => true,
                'code' => 1010,
                'msg' => 'Server error',
            ],
            'database' => [
                'error' => true,
                'code' => 1020,
                'msg' => 'Database error',
            ],
            'data' => [
                'error' => true,
                'code' => 1030,
                'msg' => 'No data found',
            ],
            'credentials' => [
                'error' => true,
                'code' => 1040,
                'msg' => 'Invaild credentials',
            ],
            'parameter' => [
                'error' => true,
                'code' => 1050,
                'msg' => 'Invaild request',
            ],
            'unavailable' => [
                'error' => true,
                'code' => 1060,
                'msg' => 'Function unavailable',
            ],
            'upload' => [
                'error' => true,
                'code' => 1070,
                'msg' => 'Upload fail',
            ],
            'unauthorised' => [
                'error' => true,
                'code' => 1080,
                'msg' => 'Unauthorised',
            ],
            'throttled' => [
                'error' => true,
                'code' => 1090,
                'msg' => 'Too Many Attempts.',
            ],
        ],
        'success' => [
            'error' => false,
            'code' => 0,
        ],
    ],
];