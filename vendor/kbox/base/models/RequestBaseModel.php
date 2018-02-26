<?php

namespace kbox\base\models;

use kbox\base\libs\AppFunc;
use kbox\base\components\exception\Exception;


class RequestBaseModel extends BaseModel
{
    static $_user = null;

    private $user;

    public $token;

    public function rules()
    {
        return [
            [['token'], 'required'],
            ['token', 'string'],
        ];
    }

    /**
     * @return mixed
     */
    public function getUser(){
        //根据token获取userId
        if(!empty(self::$_user)) return self::$_user;

        if(empty(\Yii::$app->params['usercenter_url'] )){
            throw new Exception('请设置用户中心',Exception::ERROR_CONFIG);
        }

        $getUserUrl = \Yii::$app->params['usercenter_url'] . '/common-api/check-platform-auth';
        $ret =  AppFunc::curlPostWithHttpInfo($getUserUrl);
        $ret = json_decode($ret,true);
        if($ret['code'] != 0){
            throw new Exception($ret['message'],Exception::ERROR_API_USERCENTER);
        }
        $user = $ret['data'];
        if(!empty(\Yii::$app->params['dbmodel_user'])){
            $classAuthUser = \Yii::$app->params['dbmodel_user'];
            $userSelf = $classAuthUser::findOneById($user['id']);
            if(empty($userSelf)){
                throw new Exception("用户不存在",Exception::ERROR_USER);
            }
            $user = array_merge($user,$userSelf);
        }

       self::$_user = $user;
        return self::$_user;
    }

    public function setUser($user){
        if(!empty($user)){
            self::$_user = $user;
        }
    }
}