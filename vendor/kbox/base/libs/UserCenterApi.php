<?php
namespace kbox\base\libs;
use kbox\base\components\exception\Exception;

class UserCenterApi{

    const API_USERLIST = "/auth/api/user-list-by-platform-where";//仅当前平台
    const API_USERLIST_All = "/auth/api/user-list-by-where";//仅看用户
    const API_USERLIST_PAGE = "/auth/api/user-list-page-by-platform-where";//当前平台用户分页

    public static function curlCenter($url,$data){
        //如果需要指定 platform_id 请设置$data['platform_id'] = xxxx;
        $ret = AppFunc::curlPostWithHttpInfo(\Yii::$app->params['usercenter_url'].$url,$data);
        $ret = json_decode($ret,true);
        if($ret['code'] == 0){
            return $ret['data'];
        }else{
            throw new Exception($ret['message'],Exception::ERROR_API_USERCENTER);
        }
    }

    public static function findListByWhere($where,$onlyPlat,$where2=[]){
        //where必须有
        $data = ['where'=>$where,'where2'=>$where2];
        $apiUrl = $onlyPlat ? self::API_USERLIST : self::API_USERLIST_All;
        $userList = self::curlCenter($apiUrl,$data);
        if(empty($userList)){
            return [];
        }
        $userList = array_values($userList);
        return $userList;
    }

    public static function findPageListByWhere($page,$pagesize,$where=[],$where2=[]){
        //page,pagesize必须有
        $data = ['where'=>$where,'where2'=>$where2,'page'=>$page,'pagesize'=>$pagesize];
        $apiUrl = self::API_USERLIST_PAGE;
        $userList = self::curlCenter($apiUrl,$data);
        return $userList;
    }



    public static function findOneByWhere($where,$onlyPlat,$where2=[]){
        $userList = self::findListByWhere($where,$onlyPlat,$where2);
        return isset($userList[0]) ? $userList[0] : [];
    }

    public static function findOneByMobile($mobile,$onlyPlat){
        $where = ['mobile'=>$mobile];
        return self::findOneByWhere($where,$onlyPlat);
    }

    public static function findOneById($id,$onlyPlat){
        $where = ['id'=>$id];
        return self::findOneByWhere($where,$onlyPlat);
    }



}
