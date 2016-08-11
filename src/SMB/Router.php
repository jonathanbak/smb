<?php
/**
 * Match URI to Class File
 * User: jonathan.bak
 * Date: 2016. 5. 2.
 */

namespace SMB;

class Router
{
    /**
     * Router Singleton
     * @var null
     */
    public static $instance = null;

    /**
     * User Defined Route Information
     * @var array
     */
    protected $routes = array();

    /**
     * Singleton pattern
     * @return null|Router
     */
    public static function getInstance(){
        if(self::$instance==null) self::$instance = new self();
        
        return self::$instance;
    }

    /**
     * special Function, Class 내 protected function 을 static 형태로 호출하게 되면 자동 연결해주는 함수
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();
        switch (count($args)) {
            case 0:
                return $instance->{$method}();
            case 1:
                return $instance->{$method}($args[0]);
            case 2:
                return $instance->{$method}($args[0], $args[1]);
            case 3:
                return $instance->{$method}($args[0], $args[1], $args[2]);
            case 4:
                return $instance->{$method}($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }

    /**
     * 자동 라우팅 처리 start
     * @throws ConfigureException
     * @throws DirectoryException
     */
    protected function autoload()
    {
        System::config();
        $siteConfig = Configure::site();
        $siteNamespace = $siteConfig['namespace']."\\";

        Autoloader::setPsr4(array(
            $siteNamespace => array(Directory::sitePath('controller')),
            $siteNamespace."Model\\"=> array(Directory::sitePath('model'))
        ));
        //실제 작업 시작
        $this->execute();
    }

    /**
     * URL 을 변환해 준다
     * @param string $forwardUri
     * @return array
     */
    protected function getUri( $forwardUri = '' ){
        $uri = $forwardUri? $forwardUri : $_SERVER['REQUEST_URI'];
        $arrTmpUrl = explode('?',$uri);
        if(isset($arrTmpUrl[1])) {
            $params = array();
            parse_str($arrTmpUrl[1], $params);
            $_GET = array_merge($_GET, $params);
        }
        $url = $arrTmpUrl[0];
        $arrUri = explode('/', $url );
        if(preg_match("/^\//i",$uri,$tmpMatch)) array_shift($arrUri);
        return $arrUri;
    }

    /**
     * Image, Javascript, CSS 파일등 정적파일 출력
     * @param string $staticUri
     * @throws ConfigureException
     * @throws DirectoryException
     */
    protected function staticFiles( $staticUri = ''){
        $charset = Configure::site('charset');
        if(preg_match('/^(js|css|images){1}\/(.+)/i',$staticUri,$tmpMimeMatch)){
            $mimeFilePath = '';
            switch($tmpMimeMatch[1]){
                case 'images':
                    $mimeFilePath = Directory::sitePath('view.image') . DIRECTORY_SEPARATOR .$tmpMimeMatch[2];
                    $imageInfo = getimagesize($mimeFilePath);
                    header("Content-type: {$imageInfo['mime']}; charset=".strtoupper($charset));
                    break;
                case 'js':
                    $mimeFilePath = Directory::sitePath('view.js') . DIRECTORY_SEPARATOR .$tmpMimeMatch[2];
                    header("Content-Type: application/javascript; charset=".strtoupper($charset));
                    break;
                case 'css':
                    $mimeFilePath = Directory::sitePath('view.css') . DIRECTORY_SEPARATOR .$tmpMimeMatch[2];
                    header("Content-type: text/css; charset=".strtoupper($charset));
                    break;
                default:
                    //$mimeFilePath = $arrDirs['html'] . '/' .$ctrFileName;
                    break;
            }
            if(is_file($mimeFilePath)){
                echo file_get_contents($mimeFilePath);
            }else{
                $this->error("Not Found File. {$mimeFilePath}",404);
            }
            exit;
        }
    }

    /**
     * CLI 모드 rounting 정보 작성
     * 해당 URI에 대해 CLI 모드에서만 동작하게 만든다
     * @param $uri
     * @param string $action
     * @throws Exception
     */
    protected function cli($uri, $action = '')
    {
        if(empty($action)) {
            $action = function(){
                System::execute();
            };
        }else{
            $uri = $action;
            $action = function() use($uri){
                System::execute($uri);
            };
        }
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * GET 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function get($uri, $action)
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * POST 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function post($uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * GET,POST등 모든 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function any($uri, $action)
    {
        $verbs = array('GET', 'HEAD', 'POST');
        return $this->addRoute($verbs, $uri, $action);
    }

    /**
     * 라우팅 정보 추가
     * @param $method
     * @param $uri
     * @param $action
     */
    protected function addRoute( $method, $uri, $action )
    {
        $this->routes[] = array($method, $uri, $action);
    }

    /**
     * 최초 라우팅 실행 - URI과 클래스를 매칭하여 실행한다
     * @throws ConfigureException
     * @throws RouterException
     */
    protected function execute()
    {
        //현재 URL 에 맞는 route 실행
        $routeConfig = Configure::site('route');
        $currentUri = $this->getUri();

        $this->staticFiles(implode(DIRECTORY_SEPARATOR,$currentUri));

        if(count($this->routes)===0){

        }else{
            $arrUri = $this->getUri();
            foreach($this->routes as $key => $route ){
                list($method, $uri, $action) = $route;
                if(!is_array($method)) $method = array($method);
                if(preg_match('/^\//i',$uri,$tmpMatch)){
                    $uri = substr($uri, 1);
                }
                if($uri == implode('/', $arrUri) && in_array(strtoupper($_SERVER['REQUEST_METHOD']),$method) ){
                    if(is_object($action)){
                        $currentUri = $action;
                    } else{
                        $currentUri = $this->getUri($action);
                    }
                }
            }
        }

        if(is_object($currentUri)){
            call_user_func_array($currentUri, array());
        }else{
            if(count($currentUri)==1 && empty($currentUri[0])){
                $currentUri = $this->getUri($routeConfig['autoload']);
            }
            $this->callClassByUri( $currentUri );
        }
    }

    /**
     * URI 와 Class 매칭
     * @param $currentUri
     * @throws ConfigureException
     * @throws RouterException
     */
    protected function callClassByUri( $currentUri )
    {
        $siteNamespace = Configure::site('namespace');
        $loadClassName = $siteNamespace . '\\' . implode('\\', $this->ucfirstArray($currentUri));
        if(class_exists($loadClassName)) {
            $callClass = new $loadClassName();
        }else{
            $method = array_pop($currentUri);
            $loadClassName = $siteNamespace . '\\' . implode('\\', $this->ucfirstArray($currentUri));
            if(class_exists($loadClassName)){
                $callClass = new $loadClassName();
                $callClass->$method();
            }else {
                throw new RouterException("Not Found Class File - ". $loadClassName);
            }
        }
    }

    /**
     * 배열 값을 각각 ucfirst 하여 돌려줌
     * @param $values
     * @return array
     */
    protected function ucfirstArray( $values )
    {
        $response = array();
        foreach( $values as $val){
            $response[] = ucfirst($val);
        }
        return $response;
    }
}

class RouterException extends Exception {

}