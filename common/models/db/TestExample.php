<?php
namespace common\models\db;

use Yii;

use common\models\base\BaseActiveRecord;
use common\models\logic\Status;

class TestExample extends BaseActiveRecord
{


    public static function tableName()
    {
        return 'teat_example';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function add($params){
        $model = new self;
        foreach($params as $k=>$v){
            $model->$k = $v;
        }
        $model->insert();
        return $model->qa_id;
    }

    public static function findOneById($id){
        return self::find()->where(['id'=>$id,'status'=>Status::VALID])->asArray(true)->one();
    }
}
