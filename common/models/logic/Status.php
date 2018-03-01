<?php
namespace common\models\logic;

class Status {

    const VALID = 0;//正常状态
    const DELETE = 1;//删除状态

    const ALL_LIST = [self::VALID,self::DELETE];
}