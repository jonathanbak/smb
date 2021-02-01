<?php
namespace SMB;

/**
 * Load Configure file (configure.json)
 * @author jonathan bak
 *
 */
class Directory extends Singleton
{
    const CONFIG = 'config';
    const CONFIG_SITE = 'config/site';
    const CONFIG_DB = 'config/db';
    const HTML = 'html';
    const SYSTEM = 'system';
    const LIBRARY = 'library';
    const APP = 'app';

    public $baseDir = '';
    public $rootDir = '';

    protected function root()
    {
        return $this->rootDir ? $this->rootDir : '.'  ;
    }

    protected function setRoot( $rootDir = '' )
    {
        $this->rootDir = $rootDir;
    }

    protected function setBase( $baseDir = '' )
    {
        $this->baseDir = self::root() . DIRECTORY_SEPARATOR . ($baseDir? $baseDir . DIRECTORY_SEPARATOR : '');
    }

    protected function base()
    {
        return $this->baseDir ? $this->baseDir : self::root() . DIRECTORY_SEPARATOR  ;
    }

    protected function app()
    {
        $dir = $this->base() . self::APP;
        return $dir;
    }

    protected function config()
    {
        $dir = $this->base() . self::CONFIG;
        return $dir;
    }

    protected function config_site()
    {
        $dir = $this->base() . self::CONFIG_SITE;
        return $dir;
    }

    protected function config_db()
    {
        $dir = $this->base() . self::CONFIG_DB;
        return $dir;
    }

    protected function html()
    {
        $dir = $this->base() . self::HTML;
        return $dir;
    }

    protected function system()
    {
        $dir = $this->base() . self::SYSTEM;
        return $dir;
    }

    protected function library()
    {
        $dir = $this->base() . self::LIBRARY;
        return $dir;
    }

    protected function siteRoot()
    {
        $siteConfig = Configure::site('dirs');
        if (empty($siteConfig['root'])) throw new DirectoryException("site root 디렉토리 설정이 잘못되었습니다.");

        if (preg_match('/^[~]/i', $siteConfig['root'], $tmpMatch)) $dir = str_replace('~', $this->app(), $siteConfig['root']);
        else $dir = $this->base()  . $siteConfig['root'];

        return $dir;
    }

    protected function sitePath($dir)
    {
        $siteConfig = Configure::site('dirs');
        if(preg_match('/[.]/i',$dir,$tmpMatch)){
            $dirs = explode('.',$dir);
            if (empty($siteConfig[$dirs[0]][$dirs[1]])) throw new DirectoryException("site " . $dir . " 디렉토리 설정이 잘못되었습니다.");
            $dir = $this->siteRoot() . DIRECTORY_SEPARATOR . $siteConfig[$dirs[0]][$dirs[1]];
        }else{
            if (empty($siteConfig[$dir])) throw new DirectoryException("site " . $dir . " 디렉토리 설정이 잘못되었습니다.");
            $dir = $this->siteRoot() . DIRECTORY_SEPARATOR . $siteConfig[$dir];
        }

        return $dir;
    }
}

class DirectoryException extends Exception
{

}