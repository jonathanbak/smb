<?php
namespace SMB;
/**
 * 호출하는 php 파일을 서버 백그라운드에서 실행한다
 * User: jonathan.bak
 * Date: 2016. 2. 23.
 */
class System extends Object
{
    const MULTI = false;
    const ONE = true;

    private $logFile = "/system_log_{date}.log";
    private $pidFile = '/system_bg_{pid}.pid';
    private $pwd = '';
    private $cmdFile = '';
    private $params = array();

    public function __construct()
    {
        date_default_timezone_set('Asia/Seoul');
    }

    public function setLogFilePath()
    {
        $logDir = Directory::sitePath('log');
        $logFile = str_replace('{date}',date("Ymd"),$this->logFile);
        $this->logFile = $logDir . $logFile;
    }
    public function setPidFilePath( $cmd )
    {
        $this->setLogFilePath();
        $logDir = Directory::sitePath('log');
        if(!preg_match("/^\/(.+)/i",$cmd, $tmpMatch)){
            $cmd = "/". $cmd;
        }
        $this->cmdFile = $logDir . $cmd;
        $this->pidFile = $logDir . $this->pidFile;
        $cmdPid = md5($cmd);
        $this->pidFile = str_replace('{pid}',$cmdPid,$this->pidFile);
    }

    public function setParams( $params = array() )
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * 백그라운드로 시스템파일 처리
     * @param bool $blockRedundancy 중복실행 불가 Default:true, 기존 명령실행이 종료되지 않았으면 중복실행불가
     * @return bool
     */
    public function bg( $cmd, $blockRedundancy = true )
    {
        $this->setPidFilePath($cmd);
        if($this->isRunning() && $blockRedundancy){
            return false;
        }else{
            $cmdFile = $this->cmdFile . " " . implode(' ',$this->params);
            $return = exec("whereis php", $result);
//            $return = "php: /usr/bin/php /etc/php.d /etc/php.ini /usr/lib64/php /usr/include/php /usr/local/php /usr/share/php /usr/share/man/man1/php.1.gz";
            $whereIsPhpList = explode(' ',str_replace("php:","",$return));
            $phpScriptFile = '';
            foreach($whereIsPhpList as $phpFile ){
                if(is_file($phpFile)) {
                    $phpScriptFile = $phpFile;
                    break;
                }
            }
            $result = exec(sprintf($phpScriptFile. ' "'. Directory::html() . '/index.php"' ." %s >> %s 2>&1 & echo $! > %s", 'http://'.Configure::site('host') .'/' . $cmd, $this->logFile, $this->pidFile ));

            return true;
        }
    }
    
    protected function config( $cmd )
    {
        if($this->isCli()==true ){
            if(empty($_SERVER['REMOTE_ADDR'])){
                $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            }
            $pathInfo = parse_url($_SERVER['argv'][1]);
            if(empty($pathInfo['host'])){
                throw new Exception("http:://domain/path 형태로 실행할 경로를 입력하세요");
            }
            if(empty($pathInfo['path'])){
                throw new Exception("실행할 파일 경로를 입력하세요. 메인 파일 실행이면 http:://domain/ 이렇게 입력하세요");
            }
            if(preg_match('/([^:]+):([0-9]+)$/i',$pathInfo['host'],$tmpMatch)) {
                $_SERVER['SERVER_NAME'] = $tmpMatch[1];
                $_SERVER['SERVER_PORT'] = $tmpMatch[2];
            }else{
                $_SERVER['SERVER_NAME']=$pathInfo['host'];
                $_SERVER['SERVER_PORT'] = 80;
            }
            array_shift($_SERVER['argv']);array_shift($_SERVER['argv']);
            $_SERVER['argc']--;
            $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT']!='80'? ':'.$_SERVER['SERVER_PORT'] : '');
            $_SERVER['REQUEST_URI'] = $pathInfo['path'];
            $_SERVER['REQUEST_METHOD'] = 'GET';

            $this->setPidFilePath( $cmd );
            $this->cmdFile = $cmd;
        }
    }

    protected function execute( $cmd = '' )
    {
        if(empty($cmd)) $cmd = $this->cmdFile;

        if($this->isCli()==true ){
            if($this->isRunning()) throw new Exception("이미 실행중인 커맨드 입니다.");
            $this->makePidFile();
            Router::callClassByUri( Router::getUri($cmd) );
        }else {
            throw new Exception("커맨드 실행만 가능합니다.");
        }
    }

    protected function makePidFile()
    {
        file_put_contents($this->pidFile, getmypid());
    }

    public function isCli()
    {
        return php_sapi_name() == 'cli'? true : false;
    }

    public function isRunning()
    {
        try {
            if(is_file($this->pidFile)){
                $pid = file_get_contents($this->pidFile);
                $result = shell_exec(sprintf('ps %d', $pid));
                if(count(preg_split("/\n/", $result)) > 2) {
                    return true;
                }
            }
        } catch(\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return false;
    }
}