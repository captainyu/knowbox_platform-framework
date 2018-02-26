<?php

return
    [
        'traceLevel' => YII_DEBUG ? 10 : 0,
        'targets' => [
            //info
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'categories' => ['access'],
                'logFile' => '@runtime/logs/access.log.'.date('Ymd'),
                'enableRotation'=> false,
                'logVars' => [], //注释掉这行可以在log中打印$_GET和$_SERVER信息
            ],
            //console
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'categories' => ['console'],
                'logFile' => '@runtime/logs/console.log.'.date('Ymd'),
                'enableRotation'=> false,
                'logVars' => [],
            ],
            //custom
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'categories' => ['custom'],
                'logFile' => '@runtime/logs/custom.log.'.date('Ymd'),
                'enableRotation'=> false,
                'logVars' => [],
            ],
            //apierror
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['warning'],
                'categories' => ['apierror'],
                'logFile' => '@runtime/logs/apierror.log.'.date('Ymd'),
                'logVars' => [],
                'enableRotation'=> false,
            ],
            //syserror
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error'],
                'categories' => ['syserror'],
                'logFile' => '@runtime/logs/syserror.log.'.date('Ymd'),
                'logVars' => [],
                'enableRotation'=> false,
            ],
            //profile
            /*
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['profile'],
                'logFile' => '@runtime/logs/profile.log.'.date('Ymd'),
                'logVars' => [],
                'enableRotation'=> false,
            ],
            */
        ],
    ];