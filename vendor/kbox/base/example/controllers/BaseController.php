<?php
namespace kbox\base\example\controllers;


class BaseController extends \kbox\base\controllers\BaseController
{

    public function actionIndex()
    {
        return [];
    }

    public function actionError(){
        $exception = \Yii::$app->errorHandler->exception;
        throw $exception;
    }
}
