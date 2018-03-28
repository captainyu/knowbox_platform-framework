<?php
namespace  kbox\base\libs;


class AppFunc{


    /**
     * @param $data
     * @return array|int
     * 格式化number
     */
    public static function formatNumber($data){
        if(is_array($data)){
            return array_map('intval',$data);
        }
        return intval($data);
    }

    public static function curlPostWithHttpInfo($url,$data=[],$headers=[]){
        !empty(\Yii::$app->params['auth_platform_id']) && $data['auth_platform_id'] = \Yii::$app->params['auth_platform_id'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch,CURLOPT_REFERER,\Yii::$app->request->hostInfo);
        $ip = \Yii::$app->request->headers['x-real-ip'] ?? \Yii::$app->request->getUserIP();
        $headers[] = "CLIENT-IP: {$ip}";
        $headers[] = "Content-type: application/json;charset='utf-8'";
        $cookies = [];
        foreach($_COOKIE as $k=>$v){
            $cookies[] = "{$k}={$v}";
        }
        $cookiesStr = join(';',$cookies);
        $headers[] = "Cookie:{$cookiesStr}";
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        \Yii::beginProfile('POST '.$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile('POST '.$url, __METHOD__);
        curl_close($ch);
        return $result;
    }

    public static function curlPostArr($url,$data=[],$headers=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $headers[] = "Content-type: application/json;charset='utf-8'";
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        \Yii::beginProfile('POST '.$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile('POST '.$url, __METHOD__);
        curl_close($ch);
        return $result;
    }

    public static function curlPost($url,$data,$headers=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        \Yii::beginProfile('POST '.$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile('POST '.$url, __METHOD__);
        curl_close($ch);
        return $result;
    }

    public static function curlGet($url,$headers=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        \Yii::beginProfile('GET '.$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile('GET '.$url, __METHOD__);
        curl_close($ch);
        return $result;
    }


    public static function curlMethod($method,$url,$data=[],$headers=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $method = strtoupper($method);
        switch($method){
            case "GET":
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, 1);
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }
        \Yii::beginProfile($method." ".$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile($method . " ".$url, __METHOD__);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [
            'code'=>$code,
            'result'=>$result,
        ];
    }


    public static function mail($userName,$title,$htmlText){
        try{
            $urlhost = 'http://' . $_SERVER['HTTP_HOST'];
            $htmlText = $htmlText . "操作人：$userName<br/>操作网址：{$urlhost}";
            $mail= \Yii::$app->mailer->compose();
            $mail->setTo(\Yii::$app->params['tiku_mail'])
                ->setFrom(\Yii::$app->params['sendFrom'])
                ->setSubject($title)
                ->setHtmlBody($htmlText);    //发布可以带html标签的文本
            //        $mail->setTextBody($htmlText);   //发布纯文字文本
            $ret = $mail->send();
            return $ret;
        }catch(\Exception $e){
            return false;
        }

    }



    public static function joinRecursion($split,$array){
        foreach($array as $k=>$v){
            is_array($v) && $array[$k] = static::joinRecursion($split,$v);
        }
        return join($split,$array);
    }

    /**
     * 删除指定标签
     *
     * @param array $tags     删除的标签  数组形式
     * @param string $str     html字符串
     * @param bool $delConent   true删除标签覆盖的内容content
     * @return mixed
     */
    public static function stripHtmlTags($str,$tags = [], $delConent = false){
        if(empty($tags)){
            $tags = ['p','span','div'];
        }
        $html = [];
        // 是否保留标签内的text字符
        if($delConent){
            foreach ($tags as $tag) {
                $html[] = '/(<' . $tag . '.*?>(.|\n)*?<\/' . $tag . '>)/is';
            }
        }else{
            foreach ($tags as $tag) {
                $html[] = "/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/is";
            }
        }
        $data = preg_replace($html, '', $str);
        return $data;
    }

    function getServerIp() {
        exec('ifconfig | grep "10\\.10\\.255\\.255"', $out, $stats);
        if (!empty($out)) {
            $preg = "/(?:(?:1[0-9][0-9]\.)|(?:2[0-4][0-9]\.)|(?:25[0-4]\.)|(?:[1-9][0-9]\.)|(?:[0-9]\.)){3}(?:(?:1[0-9][0-9])|(?:2[0-4][0-9])|(?:25[0-4])|(?:[1-9][0-9])|(?:[0-9]))/";
            $match = preg_match($preg,$out[0],$matches);
            if($match){
                return $matches[0];
            }
        }
        return '';
    }
}
