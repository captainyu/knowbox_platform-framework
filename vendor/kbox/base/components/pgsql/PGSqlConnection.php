<?php
namespace kbox\base\components\pgsql;

use yii\db\Connection;

class PGSqlConnection extends  Connection {
    public $commandClass = 'yii\db\Command';
    private $pgc;

    public function open()
    {
        if ($this->pgc != null){
            return $this->pgc;
        }
        $this->pgc = pg_connect($this->getDsn());
        return $this->pgc;
    }

    public function getDsn(){
        $arr = explode(":", $this->dsn);
        $pairs = explode(";" , array_pop($arr));
        $pairs[] = "user=".$this->username;
        $pairs[] = "password=".$this->password;
        return implode(" ", $pairs);
    }

    public function createCommand($sql = null, $params = [])
    {
        $command = new $this->commandClass([
            'db' => $this,
            'sql' => $sql,
        ]);

        return $command->bindValues($params);
    }
}