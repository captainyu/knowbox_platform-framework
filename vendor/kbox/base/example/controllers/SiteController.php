<?php
namespace kbox\base\example\controllers;

use kbox\base\controllers\BaseController;
use Yii;

class SiteController extends BaseController
{

    public function actionIndex()
    {
        return [];
    }

    public function actionError(){
        $exception = Yii::$app->errorHandler->exception;
        throw $exception;
    }
}
