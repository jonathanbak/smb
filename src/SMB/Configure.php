<?php
namespace SMB;

/**
 * Load Configure file (configure.json)
 * @author jonathan bak
 *
 */
class Configure
{
    const BASE = 'configure';
    const EXTENSION = 'json';
    const DOT = '.';

    static $base = null;
    static $site = array();
    static $db = array();
    static $currentSite = null;

    public static function load($configFileName = '')
    {
        $configFileName = empty($configFileName)? Directory::CONFIG . DIRECTORY_SEPARATOR . self::DEFAULT_INCLUDE_FILE . self::DOT . self::EXTENSION : Directory::CONFIG_SITE . DIRECTORY_SEPARATOR . $configFileName .self::DOT . self::EXTENSION ;
        $configFile = Directory::base() . DIRECTORY_SEPARATOR . $configFileName;
        $configure = file_get_contents($configFile);
        return json_decode($configure, true);
    }

    public static function getBaseFile()
    {
        return Directory::base() . DIRECTORY_SEPARATOR . self::BASE . self::DOT . self::EXTENSION;
    }

    public static function base()
    {
        if(self::$base == null){
            $configFileName = self::getBaseFile();
            if(is_file($configFileName)==false) throw new ConfigureException("기본 설정 파일을 찾을수 없습니다. (".$configFileName.")");
            $configure = file_get_contents($configFileName);
            self::$base = json_decode($configure, true);
        }
        return self::$base;
    }

    public static function setCurrentSite( $siteUrl )
    {
        self::$currentSite = $siteUrl;
    }

    public static function getCurrentSite()
    {
        return !empty(self::$currentSite)? self::$currentSite : $_SERVER['HTTP_HOST'];
    }

    public static function site( $key = '' )
    {
        $config = self::base();
        $selectSite = self::getCurrentSite();
        $currentSiteAlias = $selectSite;
        foreach($config['siteConfigure'] as $siteUrl => $siteAlias){
            if( $selectSite == $siteUrl ){
                $currentSiteAlias = $siteAlias;
            }
        }
        //사이트 URL과 일치하는 config 파일을 찾을수 없습니다.
        if(!$currentSiteAlias) throw new ConfigureException('Not Found Site config file.');

        if(isset($config['baseDir'])) Directory::setBase($config['baseDir']);

        if(empty(self::$site[$currentSiteAlias])){
            $configFileName = Directory::base() . DIRECTORY_SEPARATOR . Directory::CONFIG_SITE . DIRECTORY_SEPARATOR . $currentSiteAlias . self::DOT . self::EXTENSION;
            if(is_file($configFileName)==false) throw new ConfigureException($currentSiteAlias." 사이트 설정 파일을 찾을수 없습니다. (".$configFileName.")");
            $configure = file_get_contents($configFileName);
            self::$site[$currentSiteAlias] = json_decode($configure, true);
        }
        return $key? self::$site[$currentSiteAlias][$key] : self::$site[$currentSiteAlias];
    }

    public static function setSite($host , $redirectSite = '')
    {
        if(!$redirectSite) $redirectSite = $host;
        self::$base['siteConfigure'][$host] = $redirectSite;
    }

    public static function setSiteConfigure($redirectSite, $siteConfigure = array())
    {
        self::$site[$redirectSite] = $siteConfigure;
    }

    public static function db( $dbName = '',  $dbInfoFile = '')
    {
        if(!$dbInfoFile) $dbInfoFile = self::site('dbset');
        if(empty(self::$db[$dbInfoFile])){
            $dbConfigFile = Directory::config_db() . DIRECTORY_SEPARATOR . $dbInfoFile . self::DOT . self::EXTENSION;
            if(is_file($dbConfigFile)==false) throw new ConfigureException($dbInfoFile." 디비 설정 파일을 찾을수 없습니다. (".$dbConfigFile.")");
            $configure = file_get_contents($dbConfigFile);
            self::$db[$dbInfoFile] = json_decode($configure, true);
        }

        return $dbName? self::$db[$dbInfoFile][$dbName] : self::$db[$dbInfoFile];
    }
}

class ConfigureException extends Exception
{

}