<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'language'=>'zh-CN',
    'id' => 'api-knowbox',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'kbox\base\example\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'common' => [
            'class' => 'kbox\base\example\modules\common\Module',
        ],
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfValidation'=>false
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'log' => [
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
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],


    ],
    'params' => $params,
];
