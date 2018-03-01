<?php
namespace kbox\base\example\modules\common\controllers;

use kbox\base\example\controllers\BaseController;

class ExampleController extends BaseController {


    public function actionTest(){
        //业务场景
        $model = new Example(['scenario'=>Example::SCENARIO_TEST]);
        //传入参数
        $model->load($this->loadData);
        //参数校验
        $model->validate();
        $ret = $model->test();
        return $ret;
    }
}