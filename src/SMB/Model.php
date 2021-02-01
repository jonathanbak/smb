<?php
/**
 * Model
 * User: jonathan.bak
 * Date: 2016. 5. 4.
 */

namespace SMB;


class Model extends Singleton
{
    protected $db = null;

    public function __construct()
    {
        $dbsetList = Configure::db();
        $dbName = array_keys($dbsetList)[0];
        if($dbName){
            $this->connect($dbName);
        }
    }

    public function connect($dbName)
    {
        $this->db = Db::connect($dbName);
    }
}