<?php

namespace kbox\base\libs;

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use kbox\base\components\exception\Exception;

class Qiniu
{

    const ACCESSKEY = 'nJL7e6J3VAIC5j4DqE-KKUeOv2LkGTaN4YjXBw7F';
    const SECRETKEY = 'n4Yt7k2bNwvA7rS4ETENhl_cGhnsS9hUrGFamwvX';
    const BUCKET = 'tiku-img';

    public static function getImageInfo($imgUrl){
        //{"size":52936,"format":"png","width":443,"height":68,"colorModel":"nrgba"}
        $ret = AppFunc::curlGet($imgUrl.'?imageInfo');
        return json_decode($ret,true) ?? [];
    }

    public static function getInfo($key,$bucket){
        $auth = new Auth(static::ACCESSKEY, static::SECRETKEY);
        $bucketMgr = new BucketManager($auth);
        list($ret, $err) = $bucketMgr->stat($bucket, $key);
        if ($err !== null) {
            //error
            throw new Exception("获取失败",Exception::ERROR_COMMON);
        } else {
            return $ret;
        }
    }


    public static function rename($fromname,$toName){
        $auth = new Auth(static::ACCESSKEY, static::SECRETKEY);
        $bucket = static::BUCKET;
        //上传
        $model = new BucketManager($auth);
        $ret = $model->rename($bucket,$fromname,$toName);
        return $ret;
    }

    public static function uploadFile($key,$filePath){
        $auth = new Auth(static::ACCESSKEY, static::SECRETKEY);
        $bucket = static::BUCKET;
        $upToken = $auth->uploadToken($bucket);
        //上传
        $model = new UploadManager();
        $ret = $model->putFile($upToken,
            $key,
            $filePath,
            $params = null,
            $mime = 'application/octet-stream',
            $checkCrc = false);
        return $ret;
    }

    public static function uploadFileStream($key,$data,$params = null,$mime = 'application/octet-stream',$checkCrc = false){
        $auth = new Auth(static::ACCESSKEY, static::SECRETKEY);
        $bucket = static::BUCKET;
        $upToken = $auth->uploadToken($bucket);
        //上传
        $model = new UploadManager();
        $ret = $model->put($upToken,
            $key,
            $data,
            $params = null,
            $mime = 'application/octet-stream',
            $checkCrc = false);
        return $ret;
    }
}
