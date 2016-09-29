<?php
/**
 * File Controller Load
 * User: jonathan.bak
 * Date: 2016. 5. 2.
 */

namespace SMB;


class Controller
{
    const START_METHOD = 'main';
    protected $config = null;
    protected $tpl = null;

    public function __construct()
    {
        ob_start();
        $this->config = Configure::site();
        $this->tpl = new Template();
    }

    public function displayHTML( $tpl = '' )
    {
        $protoperties = get_object_vars($this);
        if(!$tpl){
            $callerClass = get_class($this);
            $callerClass = str_replace('\\',DIRECTORY_SEPARATOR,strtolower( str_replace($this->config['namespace'].'\\','',$callerClass) ));
            $callerFunc = debug_backtrace()[1]['function'];
            $tplFile = $callerClass . DIRECTORY_SEPARATOR . strtolower($callerFunc);
        }else {
            $tplFile = $tpl;
        }

        $this->tpl->displayHTML($tplFile, $protoperties);
    }

    public function __destruct()
    {
        ob_end_flush();
    }

    public function error($message, $code = -1){
        throw new Exception($message, $code);
    }
}