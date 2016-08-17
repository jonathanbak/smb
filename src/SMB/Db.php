<?php
/**
 * User: jonathan.bak
 * Date: 2015. 11. 19.
 * MySQL 접속 공용
 */

namespace SMB;


class Db extends ExtensionBridge
{
    public static $db = array();
    public static $config = null;

    public function __construct( $dbName = '' )
    {
        $options = Configure::db( $dbName );
        $options = array_merge($options, array('options' => array('buffer_results' => true)));;
        
        parent::addExt(new \Zend\Db\Adapter\Adapter($options));
    }

    /**
     * singleton 객체를 반환
     * @param string $dbName
     * @return mixed
     */
    public static function connect($dbName = '')
    {
        if (isset(self::$db[$dbName]) == false) {
            self::$db[$dbName] = new self($dbName);
        }
        return self::$db[$dbName];
    }

    public static function __callStatic($method, $args)
    {
        $lastConnectDb = array_pop(self::$db);
        array_push(self::$db, $lastConnectDb);

        return call_user_func_array(array($lastConnectDb, $method), $args);
    }

    public function getConnection()
    {
        $lastConnectDb = array_pop(self::$db);
        array_push(self::$db, $lastConnectDb);

        return $lastConnectDb->_parents[0]->driver->getConnection()->getResource();
    }

    /**
     * @param $query    실행쿼리
     * @return null|resource
     */
    public function query($query, $params = array())
    {
        return $this->_parents[0]->query($query)->execute($params);
    }

    /**
     * 요청쿼리에 맞는 데이터 여러행을 리턴
     * @param $query    데이터요청 쿼리
     * @param array $params
     * @return array
     */
    public function fetchAll($query, $params = array())
    {
        $rows = array();
        foreach ($this->query($query, $params) as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * 요청 쿼리에 맞는 데이터 한행을 리턴
     * @param $query    데이터 요청 쿼리
     * @param array $params
     * @return array|bool
     */
    public function fetch($query, $params = array())
    {
        return $this->query($query, $params)->current();
    }

    public function countRows($query, $params = array())
    {
        return $this->query($query, $params)->count();
    }

    public function fetchOne($query, $params = array())
    {
        $row = $this->query($query, $params)->current();
        return $row ? array_pop($row) : null;
    }

    /**
     * 마지막 입력 번호 가져오기 (auto increment column)
     * @return bool|int
     */
    public function lastInsertId()
    {
        $row = $this->query("select last_insert_id() as lastId", array())->current();
        return $row ? array_pop($row) : null;
// 		return $this->getDriver()->getConnection()->getLastGeneratedValue();
    }

    /**
     * SQL Injection 방어 mysql_real_escape_string 실행
     * @param array $params
     * @return array
     */
    public function arrayToRealEscape( $params = array() )
    {
        foreach($params as $k=> $value){
            $params[$k] = self::realEscapeString($value);
        }
        return $params;
    }
    
    public static function realEscapeString( $value )
    {
        return get_magic_quotes_gpc()? $value : mysqli_real_escape_string( self::getConnection(), $value);
    }

    /**
     * 숫자형 배열을 홑따옴표로 묶어준다
     * @param array $arrayVal
     * @return array
     */
    public static function intArrayQuote( $arrayVal = array() )
    {
        $tmpVal = array();
        foreach($arrayVal as $val){
            $tmpVal[] = "'".self::realEscapeString($val)."'";
        }

        return $tmpVal;
    }

    /**
     * 키:값 배열을 쿼리문에 넣기좋게 만들어준다
     * @param array $params
     * @return array
     */
    public static function parseArrayToQuery( $params = array() )
    {
        $tmpVal = array();
        foreach($params as $k => $val){
            if(preg_match('/^([0-9]+)$/i',$k,$tmpMatch)==false) $tmpVal[] = " `$k` = "." '".self::realEscapeString($val)."'";
        }
        return $tmpVal;
    }
}
