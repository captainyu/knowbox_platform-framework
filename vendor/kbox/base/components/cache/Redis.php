<?php
namespace kbox\base\components\cache;


class Redis extends \yii\redis\Connection
{
    const SWITCH_CACHE = true;

    public function executeCommand($name, $params = [])
    {
        $switchCatch = self::SWITCH_CACHE;
        if(!$switchCatch) {
            return null;
        }
        try{
            return parent::executeCommand($name,$params);
        }catch(\Exception $e){
            return null;
        }

    }
}

