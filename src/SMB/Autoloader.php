<?php
namespace SMB;

/**
 * Autoloads SMB classes
 *
 * @author    jonathan.bak
 * @license   MIT License
 */
class Autoloader
{
    protected static $composerLoader = null;

    /**
     * Registers SMB\Autoloader as an SPL autoloader.
     */
    public static function register( $composerAutoloader, $rootDir = '.' )
    {
//        ini_set('unserialize_callback_func', 'spl_autoload_call');
//
        self::setComposerLoader( $composerAutoloader );
        self::setRootDir( $rootDir );
    }

    public static function setRootDir( $rootDir = '' )
    {
        Directory::setRoot($rootDir);
    }

    /**
     * @param $composerAutoloader   composer core loader
     */
    public static function setComposerLoader( $composerAutoloader )
    {
        self::$composerLoader = $composerAutoloader;
    }

    public static function setPsr4( $map ){
        foreach ($map as $namespace => $path) {
            self::$composerLoader->setPsr4($namespace, $path);
        }
    }
}
