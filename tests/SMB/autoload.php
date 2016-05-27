<?php
/**
 * SMB autoload for phpunit test
 *
 * Date : 2016. 5. 12.
 * File : autoload.php
 *
 * @author baksangwon <jonathanbak@gmail.com>
 */

$loader = require __DIR__ . "/../../vendor/autoload.php";
$loader->addPsr4('SmbTests\\', __DIR__.'');

date_default_timezone_set('Asia/Seoul');