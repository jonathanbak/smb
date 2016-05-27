<?php
/**
 * User: jonathan.bak
 * Date: 2016. 5. 2.
 */

namespace SMB;

use SMB\Template_\Template_;

class Template extends Template_
{
    public function __construct()
    {
        $siteCompileDir = Directory::sitePath('compile');
        if(is_dir($siteCompileDir)==false) mkdir($siteCompileDir);
        $this->compile_dir = $siteCompileDir;
        $siteTplDir = Directory::sitePath('view.html');
        $this->template_dir = $siteTplDir;
    }

    public function displayHTML( $tplFile = '', $assignParams = array() )
    {
        $charset = Configure::site('charset');
        header("Content-type: text/html; charset=".strtoupper($charset));
        
        $tplName = basename($tplFile);

        $this->define($tplName, $tplFile . '.tpl');
        $this->assign($assignParams);
        $this->print_($tplName);
    }
}