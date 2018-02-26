<?php
namespace kbox\base\components\exception;

class Exception extends \yii\base\Exception {

    const ERROR_COMMON = 204;
    const ERROR_CONFIG = 300;
    const ERROR_SYSTEM = 500;
    const ERROR_VALIDATE = 501;
    const ERROR_PRIV = 403;
    const ERROR_API_USERCENTER = 1001;
    const ERROR_USER = 1002;

}