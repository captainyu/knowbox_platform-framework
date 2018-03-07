<?php

namespace kbox\base\models;

use kbox\base\components\exception\Exception;

class AuthBaseModel extends RequestBaseModel
{
    public $authRole = null;
    public $authPriv = null;

    protected $_privList = null;

    //校验用户 role_id
    public function checkAuthWithRole($exception=true){
        if(empty(\Yii::$app->params['dbmodel_user_role'])){
            throw new Exception("校验角色方式错误",Exception::ERROR_PRIV);
        }
        if(!$this->authRole){
            return true;
        }
        !is_array($this->authRole) && $this->authRole = [$this->authRole];
        $classUserRole = \Yii::$app->params['dbmodel_user_role'];
        $rolelist = $classUserRole::find()->select('role_id')->where(['user_id'=>$this->user['user_id'],'status'=>0])->asArray(true)->column();
        $hasRole = array_intersect($this->authRole,$rolelist);
        if(empty($hasRole)){
            if($exception){
                throw new Exception("权限不足",Exception::ERROR_PRIV);
            }else{
                return false;
            }
        }
        return true;
    }

    public function checkAuthWithPriv($exception=true){
        if(empty(\Yii::$app->params['dbmodel_role_priv']) || empty(\Yii::$app->params['dbmodel_user_role'])){
            throw new Exception("校验角色方式错误",Exception::ERROR_PRIV);
        }
        if(!$this->authPriv){
            return true;
        }
        if(!is_array($this->authPriv)){
            $this->authPriv = [$this->authPriv];
        }
        $userInfo = $this->getUser();
        $privListAll = $this->getPrivList($userInfo['user_id']);
        foreach ($this->authPriv as $onePriv){
            if(!empty($privListAll[$onePriv])){
                return true;
            }
        }

        if($exception){
            throw new Exception("权限不足",Exception::ERROR_PRIV);
        }
        return false;
    }

    public function getUser()
    {
        $userInfo = parent::getUser();
        $this->getPrivList($userInfo['user_id']);
        return $userInfo;
    }

    public function getPrivList($userId){
        if(empty(\Yii::$app->params['dbmodel_role_priv']) || empty(\Yii::$app->params['dbmodel_user_role'])){
            return [];
        }
        if(isset($this->_privList)){
            return $this->_privList;
        }
        //自有role
        $classUserRole = \Yii::$app->params['dbmodel_user_role'];
        $rolelist = $classUserRole::find()->select('role_id')->where(['user_id'=>$userId,'status'=>0])->asArray(true)->column();
        //权限
        $classRolePriv = \Yii::$app->params['dbmodel_role_priv'];
        $privList = $classRolePriv::find()->where(['role_id'=>$rolelist,'status'=>0])->asArray(true)->all();
        $privList = array_column($privList,null,'priv_id');
        $this->_privList = $privList;
        return $privList;
    }
}