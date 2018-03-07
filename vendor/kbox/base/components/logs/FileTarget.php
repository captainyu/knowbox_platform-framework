<?php
namespace kbox\base\components\logs;

use Yii;
use yii\log\Logger;

class FileTarget extends \yii\log\FileTarget
{

    public $beginMicroTime = 0;

    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        if($level ===  Logger::LEVEL_PROFILE_BEGIN){
            $this->beginMicroTime = intval($timestamp * 1000);
        }elseif($level === Logger::LEVEL_PROFILE_BEGIN){
            if(!empty($this->beginMicroTime)){
                $timeSpan = intval($timestamp * 1000) - $this->beginMicroTime;
                $message[0] = $text.'\t'.$timeSpan;
            }
        }
        return parent::formatMessage($message);
    }
}
