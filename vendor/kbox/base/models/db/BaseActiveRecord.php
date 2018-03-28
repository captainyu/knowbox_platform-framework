<?php
namespace kbox\base\models\db;

class BaseActiveRecord extends \yii\db\ActiveRecord {

    public static function findPageList($page,$pagesize,$where=[],$order="",$select='*',$extWhere=[]){
        !isset($where['status']) && $where['status'] = 0;
        $query = static::find()
            ->select($select)
            ->where($where);
        foreach ($extWhere as $v){
            $query = $query->andWhere($v);
        }
        $query = $query
            ->limit($pagesize)
            ->offset(($page-1)*$pagesize);
        !empty($order) && $query->orderBy($order);
         return $query->asArray(true)->all();
    }

    public static function findCount($where=[],$extWhere=[]){
        !isset($where['status']) && $where['status'] = 0;
        $query = static::find()
            ->where($where);
        foreach ($extWhere as $v){
            $query = $query->andWhere($v);
        }
        return $query->count();
    }

    public static function findOneByWhere($where,$select='*',$order=""){
        !isset($where['status']) && $where['status'] = 0;
        $query = static::find()
            ->select($select)
            ->where($where);
        !empty($order) && $query = $query->orderBy($order);
        return $query
            ->limit(1)
            ->asArray(true)
            ->one();
    }

    public static function findList($where=[],$indexKey="",$select='*'){
        !isset($where['status']) && $where['status'] = 0;
        if(!empty($indexKey)){
            return static::find()
                ->select($select)
                ->where($where)
                ->indexBy($indexKey)
                ->asArray(true)
                ->all();
        }
        return static::find()
            ->select($select)
            ->where($where)
            ->asArray(true)
            ->all();
    }

    public static function addAllWithColumnRow($columns,$rows,$split=100){
        $rowsList = array_chunk($rows,$split);
        foreach ($rowsList as $rowChunk){
            DBCommon::batchInsertAll(
                static::tableName(),
                $columns,
                $rowChunk,
                static::getDb(),
                'INSERT'
            );
        }
    }

    public static function addUpdateAllWithColumnRow($columns,$rows,$split=100){
        $rowsList = array_chunk($rows,$split);
        foreach ($rowsList as $rowChunk){
            DBCommon::batchInsertAll(
                static::tableName(),
                $columns,
                $rowChunk,
                static::getDb(),
                'UPDATE'
            );
        }
    }

}