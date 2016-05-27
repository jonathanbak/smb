<?php
/**
 * 서로 다른 클래스 연결
 * User: jonathan.bak
 * Date: 2016. 5. 3.
 */

namespace SMB;


Abstract class ExtensionBridge
{
    // array containing all the extended classes
    private $_exts = array();
    protected $_parents = array();
    public $_this;

    function __construct(){$_this = $this;}

    public function addExt($object)
    {
        $this->_exts[]=$object;
        $this->_parents = $this->_exts;
    }

    public function __get($varname)
    {
        if(!property_exists($this,$varname)){
            foreach($this->_exts as $ext)
            {
                if(property_exists($ext,$varname))
                    return $ext->$varname;
            }
        }
    }

    public function __call($method,$args)
    {
        if(!method_exists($this,$method)){
            foreach($this->_exts as $ext)
            {
                if(method_exists($ext,$method))
                    return call_user_method_array($method,$ext,$args);
            }
            throw new Exception("This Method {$method} doesn't exists");
        }
    }

}