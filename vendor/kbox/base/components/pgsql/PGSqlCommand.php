<?php
namespace kbox\base\components\pgsql;

use yii\db\Command;
use Yii;

class PGSqlCommand extends Command {
    private $_sql;

    public function query()
    {
        return $this->queryPgsqlInternel();
    }

    public function queryAll($fetchMode = null)
    {
        $res = $this->queryPgsqlInternel();
        $fetchMode = $fetchMode === null ? $this->fetchMode : $fetchMode;
        $method = $fetchMode == \PDO::FETCH_ASSOC ? "pg_fetch_assoc" : "pg_fetch_array";
        $result = [];
        while(($row = call_user_func_array($method, [$res])) !== false){
            $result[] = $row;
        }
        return $result;
    }

    public function queryOne($fetchMode = null)
    {
        $res = $this->queryPgsqlInternel();
        $fetchMode = $fetchMode === null ? $this->fetchMode : $fetchMode;
        $method = $fetchMode == \PDO::FETCH_ASSOC ? "pg_fetch_assoc" : "pg_fetch_array";
        return call_user_func_array($method, [$res]);
    }

    public function queryScalar()
    {
        $result = $this->queryOne(\PDO::FETCH_NUM);
        return is_array($result) ? $result[0] : false;
    }

    public function queryColumn()
    {
        $res = $this->queryPgsqlInternel();
        return pg_fetch_all_columns($res, 0);
    }

    protected function queryPgsqlInternel(){
        $conn = $this->db->open();
        $rawSql = $this->getRawSql();
        \Yii::info($rawSql, __METHOD__);
        Yii::beginProfile($rawSql, __METHOD__);
        $res = pg_query($conn, $rawSql);
        Yii::endProfile($rawSql, __METHOD__);
        return $res;
    }

    public function getRawSql()
    {
        if (empty($this->params)) {
            return $this->_sql;
        } else {
            $params = [];
            foreach ($this->params as $name => $value) {
                if (is_string($value)) {
                    $params[$name] = pg_escape_literal($value);
                } elseif ($value === null) {
                    $params[$name] = 'NULL';
                } else {
                    $params[$name] = $value;
                }
            }
            if (isset($params[1])) {
                $sql = '';
                foreach (explode('?', $this->_sql) as $i => $part) {
                    $sql .= (isset($params[$i]) ? $params[$i] : '') . $part;
                }

                return $sql;
            } else {
                return strtr($this->_sql, $params);
            }
        }
    }

    /**
     * Returns the SQL statement for this command.
     * @return string the SQL statement to be executed
     */
    public function getSql()
    {
        return $this->_sql;
    }

    /**
     * Specifies the SQL statement to be executed.
     * The previous SQL execution (if any) will be cancelled, and [[params]] will be cleared as well.
     * @param string $sql the SQL statement to be set.
     * @return static this command instance
     */
    public function setSql($sql)
    {
        if ($sql !== $this->_sql) {
            $this->_sql = $this->db->quoteSql($sql);
            $this->params = [];
        }

        return $this;
    }

    public function execute()
    {
        $sql = $this->getSql();

        $rawSql = $this->getRawSql();

        $conn = $this->db->open();

        Yii::info($rawSql, __METHOD__);

        if ($sql == '') {
            return 0;
        }

        $token = $rawSql;
        try {
            Yii::beginProfile($token, __METHOD__);

            pg_prepare($conn,$rawSql,$rawSql);
            $result = pg_execute($conn,$rawSql,[]);
            $n = pg_affected_rows($result);

            Yii::endProfile($token, __METHOD__);

            return $n;
        } catch (\Exception $e) {
            Yii::endProfile($token, __METHOD__);
            throw $this->db->getSchema()->convertException($e, $rawSql);
        }
    }
}