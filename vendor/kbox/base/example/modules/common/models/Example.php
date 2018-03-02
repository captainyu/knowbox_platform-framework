<?php
namespace kbox\base\example\modules\common\models;
use kbox\base\example\models\AuthBaseModel;

class Example extends AuthBaseModel {

    //场景名称
    const SCENARIO_TEST = "SCENARIO_TEST";

    //参数
    public $test_param;

    //场景接收参数同时声明场景 场景参数必须是类public属性名
    public function scenarios()
    {
        $scenarios =  parent::scenarios();
        $scenarios[self::SCENARIO_TEST] = ['test_param'];
        return $scenarios;
    }

    //规则 规则对象必须是类public属性名
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['test_param'],'integer'],
            [['test_param'],'required','on'=>self::SCENARIO_TEST]
        ]);
    }

    public function test(){
        //业务逻辑
        //....
        //业务返回 或者 无返回值
        //return [];
    }
}