<?php

namespace kbox\base\models;

use kbox\base\components\exception\Exception;
use yii\base\Model;


class BaseModel extends Model
{

    public $sourceLoadData;

   public function getErrorString(){
       $error = "";
       foreach($this->getFirstErrors() as $k=>$v){
           $error .= $v;
       }
       return $error;
   }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $ret  = parent::validate($attributeNames, $clearErrors);
        if(false === $ret){
            throw new Exception($this->getErrorString(),Exception::ERROR_VALIDATE);
        }
        return $ret;
    }

    public function load($data, $formName = null)
    {
        $this->sourceLoadData = $data;
        if($formName === null){
            $data = ['dataForm'=>$data];
            $formName = 'dataForm';
        }
        return parent::load($data, $formName);
    }
}