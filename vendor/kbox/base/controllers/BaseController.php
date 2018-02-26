<?php
namespace kbox\base\controllers;

use kbox\base\components\exception\Exception;
use kbox\base\components\logs\Log;
use kbox\base\models\AuthBaseModel;
use Yii;

use yii\web\Controller;


class BaseController extends Controller
{
    public $enableCsrfValidation = false;
    protected $loadData;
    protected $response;
    public $extLog = [];
    private $_timestart;
    private $_userid = 0;

    protected $returnOri = false;
    protected $isEnd = false;//true不再封装 不再记录日志等信息

    protected $checkAuthType = 'priv';

    public function init()
    {
        $this->_timestart = intval(microtime(true) * 1000);
        $this->response = yii::$app->response;
        $this->response->format = \yii\web\Response::FORMAT_JSON;
        $this->loadData = \Yii::$app->request->post();
        $token = \Yii::$app->request->get('token','');
        empty($token) && isset($_COOKIE['UCENTER_IUCTOKEN']) && $token = $_COOKIE['UCENTER_IUCTOKEN'];
        if($token){
            $this->loadData['token'] = $token;
        }
        parent::init();
    }

    /**
     * 格式化正确返回值
     * @param $data
     * @return array
     */

    protected function success($data=[],$returnOri = false){
        if($this->isEnd){
            return $data;
        }
        $timeUsed = intval(microtime(true) * 1000) - $this->_timestart;
        if($returnOri){
            //返回原始数据
            Log::info([
                'timeUsed'      => $timeUsed,
                'userId'        => $this->_userid,
            ]);
            return $data;
        }
        Log::info([
            'timeUsed'      => $timeUsed,
            'userId'        => $this->_userid,
            'resultData'        => $data,
        ]);
        $result = [
            'code'      => 0,
            'message'   => 'successs',
            'product'   => \Yii::$app->params['product_id'] ?? 0,
            'requestId'   => Yii::$app->params['requestId'] ?? "",
            'data'      => $data
        ];
        $this->isEnd = true;
        return $result;
    }

    protected function  error(\Exception $exception){
        $timeUsed = intval(microtime(true) * 1000) - $this->_timestart;
        $data = [];
        if($exception instanceof Exception){
            $message = $exception->getMessage();
            $code = $exception->getCode() + 10000;
            Log::info([
                'timeUsed'      => $timeUsed,
                'userId'        => $this->_userid,
                'exception'     => "custom_error"
            ]);
            Log::error($exception);
        }elseif(YII_DEBUG){
            $message = $exception->getMessage();
            $code = Exception::ERROR_SYSTEM;
            $data = [
                'code'=>$exception->getCode(),
                'file'=>$exception->getFile(),
                'line'=>$exception->getLine(),
                'message'=>$exception->getMessage(),
                'previous'=>$exception->getPrevious(),
                'trace'=>$exception->getTraceAsString()
            ];
            Log::info([
                'timeUsed'      => $timeUsed,
                'userId'        => $this->_userid,
                'exception'     => "system_error"
            ]);
            Log::error($exception);
        }else{
            $message = "server error";
            $code = Exception::ERROR_SYSTEM;
            Log::info([
                'timeUsed'      => $timeUsed,
                'userId'        => $this->_userid,
                'exception'     => "system_error"
            ]);
            Log::error($exception);
        }
        $result = [
            'code'      => $code,
            'message'   => $message,
            'requestId'   => Yii::$app->params['requestId'] ?? "",
            'product'   => \Yii::$app->params['product_id'] ?? 0,
            'data'      => $data
        ];
        $this->isEnd = true;
        return $result;
    }

    public function checkAuth($priv,$exception=true){
        if(empty(\Yii::$app->params['auth_base_model'])){
            $model = new AuthBaseModel();
        }else{
            $classAuthModal = \Yii::$app->params['auth_base_model'];
            $model = new $classAuthModal();
        }
        $userInfo = $model->getUser();
        $this->_userid = $userInfo['user_id'];
        if($this->checkAuthType === 'role'){
            $model->authRole = $priv;
            return $model->checkAuthWithRole($exception);
        }elseif($this->checkAuthType === 'priv'){
            $model->authRole = $priv;
            return $model->checkAuthWithPriv($exception);
        }else{
            throw new Exception("不支持的权限校验类型",Exception::ERROR_PRIV);
        }

    }

    public function checkUser(){
        if(empty(\Yii::$app->params['auth_base_model'])){
            $model = new AuthBaseModel();
        }else{
            $classAuthModal = \Yii::$app->params['auth_base_model'];
            $model = new $classAuthModal();
        }
        $userInfo = $model->getUser();
        $this->_userid = $userInfo['user_id'];
        return $userInfo;
    }

    public function runAction($id, $params = [])
    {
        try{
            $result = parent::runAction($id, $params);
            return $this->success($result,$this->returnOri);
        }catch (\Exception $e){
            return $this->error($e);
        }
    }
}
