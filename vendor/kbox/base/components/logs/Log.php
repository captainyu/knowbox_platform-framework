<?php
namespace kbox\base\components\logs;

use kbox\base\components\exception\Exception;
use Yii;
use yii\base\Component;

class Log extends Component
{

    public static function logConfig(){
        return
            [
                'traceLevel' => YII_DEBUG ? 10 : 0,
                'targets' => [
                    //info
                    [
                        'class' => 'kbox\base\components\logs\FileTarget',
                        'levels' => ['info'],
                        'categories' => ['access'],
                        'logFile' => '@runtime/logs/access.log.'.date('Ymd'),
                        'enableRotation'=> false,
                        'logVars' => [], //注释掉这行可以在log中打印$_GET和$_SERVER信息
                    ],
                    //console
                    [
                        'class' => 'kbox\base\components\logs\FileTarget',
                        'levels' => ['info'],
                        'categories' => ['console'],
                        'logFile' => '@runtime/logs/console.log.'.date('Ymd'),
                        'enableRotation'=> false,
                        'logVars' => [],
                    ],
                    //custom
                    [
                        'class' => 'kbox\base\components\logs\FileTarget',
                        'levels' => ['info'],
                        'categories' => ['custom'],
                        'logFile' => '@runtime/logs/custom.log.'.date('Ymd'),
                        'enableRotation'=> false,
                        'logVars' => [],
                    ],
                    //apierror
                    [
                        'class' => 'kbox\base\components\logs\FileTarget',
                        'levels' => ['warning'],
                        'categories' => ['apierror'],
                        'logFile' => '@runtime/logs/apierror.log.'.date('Ymd'),
                        'logVars' => [],
                        'enableRotation'=> false,
                    ],
                    //syserror
                    [
                        'class' => 'kbox\base\components\logs\FileTarget',
                        'levels' => ['error'],
                        'categories' => ['syserror'],
                        'logFile' => '@runtime/logs/syserror.log.'.date('Ymd'),
                        'logVars' => [],
                        'enableRotation'=> false,
                    ],
                    //profile
                    /*
                    [
                        'class' => 'kbox\base\components\logs\FileTarget',
                        'levels' => ['profile'],
                        'logFile' => '@runtime/logs/profile.log.'.date('Ymd'),
                        'logVars' => [],
                        'enableRotation'=> false,
                    ],
                    */
                ],
            ];
    }

    public static function custom(array $logInfo,string $category='custom'){
        Yii::info(strtoupper($category)."\t".json_encode($logInfo,JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES), $category);
    }

    public static function console(array $extra = [],string $category='console'){
        $logInfo = [
            'params'    => Yii::$app->request->params,
            'extra'     => $extra
        ];
        Yii::info(strtoupper($category)."\t".json_encode($logInfo,JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES), $category);
    }

    public static function info(array $extra = [],string $category='access'){
        if (!Yii::$app->request->hasProperty('url')) return;
        $logInfo = [
            'url'       => Yii::$app->request->url,
            'requestId' => Yii::$app->params['requestId'],
            'params'    => Yii::$app->request->get(),
            'postData'    => Yii::$app->request->post(),
            'extra'     => $extra
        ];
        $logInfoList = [
            strtoupper($category),
            Yii::$app->request->url,
            Yii::$app->params['requestId'],
            $extra['timeUsed'] ?? 0,
            $extra['userId'] ?? 0,
            $extra['exception'] ?? "no_error",
            strval(json_encode($logInfo,JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES))
        ];
        Yii::info(join("\t",$logInfoList), $category);
    }

    public static function error(\Exception $exception){
        if (!Yii::$app->request->hasProperty('url')) return;
        if($exception instanceof Exception){
            $logInfo = [
                'url'       => Yii::$app->request->url,
                'requestId' => Yii::$app->params['requestId'],
                'params'    => Yii::$app->request->get(),
                'postData'    => Yii::$app->request->post(),
                'class'     => get_class($exception),
                'errorCode' => $exception->getCode(),
                'message'   => $exception->getMessage()
            ];
            $type = "API_ERROR";
            Yii::error($type."\t".json_encode($logInfo,JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES), 'apierror');
        }else{
            $logInfo = [
                'url'       => Yii::$app->request->url,
                'requestId' => Yii::$app->params['requestId'],
                'params'    => Yii::$app->request->get(),
                'postData'    => Yii::$app->request->post(),
                'class'     => get_class($exception),
                'errorCode' => $exception->getCode(),
                'message'   => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace'  => $exception->getTraceAsString(),
            ];
            $type = "SYS_ERROR";
            Yii::error($type."\t".json_encode($logInfo,JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES), 'syserror');
        }
    }
}
