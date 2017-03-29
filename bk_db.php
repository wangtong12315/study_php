<?php

/**
 * 通用数据库操作组件
 * 支持MYSQL和SQLITE数据库,使用PDO来操作数据库.
 * 
 * @copyright (c) 2013, Maxfs.org
 * @version 1.2
 * @author Actrace
 * @date 2014-03-21 17:17:35
 */
class Bk_db {

    private $obj = null;
    private $type = null;
    private $init = false;
    private $host = null;
    private $user = null;
    private $pass = null;
    private $name = null;
    private $charset = null;
    private $isTransaction = false;
    public $e;

    /**
     * 设置一个数据库工作连接参数
     * @param $type 链接的数据库类型，sqlite或者mysql。
     * @param $host 数据库地址，sqlite时，此处应该填数据库存放的路径。
     * @param $name 数据库名称
     * @param $user 数据库用户，sqlite时，此参数可设置为null。
     * @param $pass 数据库密码，sqlite时，此参数可设置为null。
     * @param $charset 数据库编码。默认为UTF-8。
     */
    public function __construct($type, $host, $name, $user = null, $pass = null, $charset = 'utf8') {
        $this->type = strtolower($type);
        $this->name = $name;
        $this->user = $user;
        $this->host = $host;
        $this->pass = $pass;
        $this->charset = $charset;
    }

    /**
     * 连接数据库
     * 设定好数据库工作连接参数后，使用此方法才正式连接到数据库。
     * 在需要的地方执行连接再进行数据库操作能够降低程序整体执行时间，并提高执行效率。
     * 注意：在一个程序执行周期内长时间执行与数据库无关的操作可能会被数据库服务器断开连接，此时使用此方法进行重新连接。
     * @return boolean
     */
    public function connect() {
        //为不同的数据库设置不同的连接方式
        switch ($this->type) {
            //Connect到MYSQL数据库
            case'mysql':
                try {
                    $this->obj = null;
                    $this->obj = new PDO("mysql:dbname={$this->name};host={$this->host}", $this->user, $this->pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->charset}'"));
                    $this->init = true;
                    return true;
                } catch (PDOException $e) {
                    Baserror::loger($e->getMessage());
                    return false;
                }
                break;
            //Connect到SQLITE数据库
            case'sqlite':
                try {
                    $this->obj = new PDO("sqlite:{$this->host}{$this->name}");
                    $this->Query("PRAGMA encoding = \"{$this->charset}\"");
                    $this->init = true;
                    return true;
                } catch (PDOException $e) {
                    Baserror::loger($e->getMessage());
                    return false;
                }
            default:
                return false;
        }
    }

    /**
     * 执行查询语句
     * @param $Query SQL语句，需要注意的是，SQLITE和MYSQL支持的SQL标准是不一样的。
     * @return boolean
     */
    public function query($Query, $parameters = null) {
//     	$Query = $this->tagFilter($Query);
        try {
            $arr_res = null;
            $pdo = $this->obj->prepare($Query);
            $pdo->execute($parameters);
            if (stripos(substr($Query, 0,10), 'select') !== false) {
                while ($tmp = $pdo->fetch(PDO::FETCH_ASSOC)) {
                    $arr_res[] = $tmp;
                }
            } else {
                $arr_res = $pdo->rowCount();
                if ($arr_res == 0 && stripos(substr($Query, 0,10), 'update') !== false) {
                    $arr_res = true;
                } elseif (is_int($arr_res) && $arr_res > 0) {
                    $arr_res = 100;
                } else {
                    $arr_res = false;
                }
            }
            return $arr_res;
        } catch (PDOException $e) {
            $this->e = $e;
            $code = $e->getCode();
            if ($code == 70100 || $code == 2006) {
                while (!$this->connect()) {
                    sleep(5);
                }
                return $this->query($Query);
            } else {
                Baserror::loger($e->getMessage());
                return false;
            }
        }
    }

    /**
     * 执行增加操作
     * @param string $t
     * @param array $dataArray
     * @return int   返回受影响的行数
     */
    public function insert($t, $dataArray) {
        $field = "";
        $value = "";
        $tripvalue = "";
        $parameters = array();
        if (is_array($dataArray) && count($dataArray) > 0) {
            foreach ($dataArray as $key => $val) {
                $field.="`$key`,";
                $value.="'$val',";
                $tripvalue.=":$key,";
                $parameters[":$key"]=$val;
            }
            $field = substr($field, 0, -1);
            $value = substr($value, 0, -1);
            $tripvalue = substr($tripvalue, 0, -1);
            $sql = "INSERT INTO `{$t}` ($field) VALUES ($value)";
            $safe_sql = "INSERT INTO `{$t}` ($field) VALUES ($tripvalue)";
            
//             echo $sql;die();
            
            // 写入数据库
            if ($t != 'qwb_log') {   // 对吧日志不重复记录入表中
            	$this->sql_log("insert", $t, $sql, 100);
            }
            $result = $this->query($safe_sql, $parameters);

            return $result;
        }
    }

    /**
     * 执行删除操作
     * @param string $t
     * @param array $condition
     * @return int    受影响的行数
     */
    public function delete($t, $condition) {
        $sql = "DELETE FROM `{$t}` WHERE $condition";
        $result = $this->query($sql);

        // 写入数据库
        $this->sql_log("delete", $t, $sql, $result);
     	//echo $sql;die();
        return $result;
    }

    /**
     * 执行更新操作
     * @param string $t
     * @param array $dataArray
     * @param string $condition
     * @return int   受影响的行数
     */
    public function update($t, $dataArray, $condition) {
        $str = "";
        $tripstr = "";
        $parameters = array();
        if (is_array($dataArray) && count($dataArray) > 0) {
            foreach ($dataArray as $key => $val) {
                $str.= "`$key`='{$val}',";
                $tripstr .= "`$key`=:$key,";
                $parameters[":$key"] = $val;
            }
            $str = substr($str, 0, -1);
            $sql = "UPDATE `{$t}` SET {$str} WHERE $condition";
            $tripstr = substr($tripstr, 0, -1);
            $safe_sql = "UPDATE `{$t}` SET {$tripstr} WHERE $condition";
//     		echo $sql;die();

            $result = $this->query($safe_sql, $parameters);

            // 写入数据库
            $this->sql_log("update", $t, $sql, $result);

            return $result;
        }
    }

    /**
     * 获取最后一个插入的ID
     * @param $col 指定一个字段，当表中有多个字段使用了自增字段时，可以使用此参数指定获取哪个字段。
     * @return int
     */
    public function getInertId($col = null) {
        return $this->obj->lastInsertId($col);
    }

//     /**
//      * 获取记录数
//      * @param unknown $sql
//      * @return number
//      */
//     public function get_rowCount($sql){
//     	$pdo     = $this->obj->prepare($sql);
//     	$pdo->execute();
//     	$arr_res = $pdo->rowCount();
//     	return $arr_res;
//     }

    /**
     * 获取记录数
     * @param unknown $sql
     * @param unknown $parameters
     * @return number
     */
    public function get_rowCount($sql, $parameters = null) {
        $sqls = "select count(*) as counts from ({$sql}) t";
        $tmp = $this->query($sqls, $parameters);
        $counts = !empty($tmp[0]['counts'])?$tmp[0]['counts']:0;
        return $counts;
    }

    /**
     * 将增、删、改记录写入数据库
     * @param unknown $option
     * @param unknown $code
     * @param unknown $result
     */
    public function sql_log($option, $table, $code, $result) {
//        $code = str_replace(array("`", "'", '"'), '', $code);
        $executeTime = date("Y-m-d H:i:s", time());
//        $sql = "INSERT INTO `qwb_log` (`option`,`table`,`code`,`result`,`executeTime`) VALUES ('{$option}','{$table}','{$code}','{$result}','{$executeTime}')";
        $sql = "INSERT INTO `qwb_log` (`option`,`table`,`code`,`result`,`executeTime`,`operater`) VALUES (:option,:table,:code,:result,:executeTime,:operater)";
        $parameter = array (
            ':option' => $option,
            ':table' => $table,
            ':code' => $code,
            ':result' => $result,
            ':executeTime' => $executeTime,
        	':operater' => $_SESSION['u'],
        );
        $this->query($sql, $parameter);
    }

    /**
     * 开启事务处理
     * @return bool $isTransaction
     */
    public function beginTransaction() {
        $this->isTransaction = $this->obj->beginTransaction();
        return $this->isTransaction;
    }

    /**
     * 提交事务
     */
    public function commit() {
        if ($this->isTransaction) {
            $this->obj->commit();
        }
    }

    /**
     * 事务回滚
     */
    public function rollBack() {
        if ($this->isTransaction) {
            $this->obj->rollBack();
        }
    }
    /**
     * 过滤html、css、js标签
     * @param unknown $str
     * @return mixed
     */
    public function tagFilter($str){
    	$str= htmlspecialchars_decode($str);
    	$str= preg_replace("/<(.*?)>/","",$str);
    	return $str;
    }
   

}
