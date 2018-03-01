<?php
namespace kbox\base\example\modules\common\controllers;

use kbox\base\example\controllers\BaseController;

class ExampleController extends BaseController {

    /**
     * @api {POST} /common/example/text 示例接口
     * @apiName 示例接口名称
     * @apiGroup 示例接口分组
     * @apiVersion 1.0.0
     * @apiDescription 示例接口描述
     * @apiParam tmp_parms 示例参数
     * @apiSuccess success_date 示例返回值
     * @apiParamExample {json} 请求参数示例:
     * { "content1": "This is an example content" }
     * @apiSuccessExample {json} 成功返回值示例
     * { "code" : 0, "message":"success" ,"data":[]}
     * @apiErrorExample {json} 失败返回值示例
     * { "code" : 500, "message":"error_message" ,"data":[]}
     */
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