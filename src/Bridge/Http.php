<?php
namespace Itxiao6\Route\Bridge;
/**
 * Class Http
 * @package Itxiao6\Route\Bridge
 */
class Http{
    /**
     * 定义请求
     * @var null | object
     */
    protected static $request = null;
    /**
     * 定义响应
     * @var null | object
     */
    protected static $response = null;

    /**
     * 设置请求
     * @param $response
     */
    public static function set_request($request)
    {
        self::$request = $request;
    }

    /**
     * 获取请求
     * @return null|object
     */
    public static function get_request()
    {
        return self::$request;
    }

    /**
     * 设置响应
     * @param $response
     */
    public static function set_response($response)
    {
        self::$response = $response;
    }

    /**
     * 获取响应
     * @return null|object
     */
    public static function get_response()
    {
        return self::$response;
    }
    /**
     * 输出内容
     * @param $content
     */
    public static function output($content,$type = null)
    {
        if(defined('IS_SWOOLE') && IS_SWOOLE===true){
            # 设置协议头
            self::$response->header("Content-Type",($type===null)?"text/html":$type);
            if($content!=''){
                # SWOOLE 模式
                self::$response->write($content);
            }
            # 发送状态码
            self::$response->status(200);
            # 结束请求
            self::$response->end('');
        }else{
            # 普通模式
            exit($content);
        }
    }

    /**
     * 请求获取url
     * @param string $key_word 分隔符
     * @return bool|string
     */
    public static function get_uri($key_word = '/')
    {
        if(defined('IS_SWOOLE') && IS_SWOOLE===true){
            $uri = self::$request -> server['request_uri'];
        }else{
            $uri = $_SERVER['REQUEST_URI'];
        }

        # 过滤后缀
        $uri = preg_replace('!\.aspx|\.php|\.html|\.htmls|\.aspx|\.jsp|\.asp!','',$uri);
        # 过滤GET 参数
        $uri = preg_replace('!\?.*!','',$uri);
        # 过滤尾部的分隔符
        $uri = preg_replace('!'.$key_word.'$!','',$uri);
        # 过滤头部的/
        $uri = preg_replace('!^/!','',$uri);
        # 返回uri
        return $uri;
    }

    /**
     * 获取请求url
     * @return mixed|string
     */
    public static function get_url()
    {
        if(isset($_SERVER['REQUEST_URI'])){
            $url = $_SERVER['REQUEST_URI'];
        }else{
            if(isset($_SERVER['argv'])){
                $url = $_SERVER['PHP_SELF'].'?'.$_SERVER['argv'][0];
            }else{
                $url = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
            }
        }
        # 过滤 GET 参数
        $url = preg_replace('!\?.*!','',$url);
        # 过滤头部的/
        $url = preg_replace('!^/!','',$url);
        # 返回url
        return $url;
    }

    /**
     * 获取请求的域名
     */
    public static function get_host()
    {
        if(defined('IS_SWOOLE') && IS_SWOOLE===true){
            return self::$request -> header['host'];
        }else{
            return $_SERVER['HTTP_HOST'];
        }
    }
    /**
     * Http重定向
     * @param $url
     */
    public static function redirect($url){
        exit(header('Location:'.$url));
    }
    /**
     * 是否为CLI环境
     * @return bool
     */
    public static function IS_CLI(){
        return PHP_SAPI=='cli'?true:false;
    }
    /**
     * 是否为CGI方式访问
     * @return bool
     */
    public static function IS_CGI(){
        return (0 === strpos(PHP_SAPI,'cgi') ||
            false !== strpos(PHP_SAPI,'fcgi')) ? true : false ;
    }
    /**
     * 获取REQUEST_METHOD
     * @return mixed
     */
    public static function REQUEST_METHOD(){
        return $_SERVER['REQUEST_METHOD'];
    }
    /**
     * 是否为PUT协议访问
     * @return bool
     */
    public static function IS_PUT(){
        return self::REQUEST_METHOD() =='PUT' ? true : false;
    }
    /**
     * [ isWechat 数组转换成XML ]
     * @return Bool 是否为微信打开
     */
    public static function IS_WECHAT()
    {
        return !(false === strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'));
    }
    /**
     * 是否为POST 请求
     * @return bool
     */
    public static function IS_POST(){
        return self::REQUEST_METHOD() =='POST' ? true : false;
    }
    /**
     * 是否为AJAX 请求
     * @return bool
     */
    public static function IS_AJAX(){
        return \Whoops\Util\Misc::isAjaxRequest();
    }
    /**
     * 是否为GET 请求
     * @return bool
     */
    public static function IS_GET(){
        return self::REQUEST_METHOD() =='GET' ? true : false;
    }
    /**
     * getClientIp 获取客户端ip
     * @return String 访问者的ip
     */
    public static function getClientIp()
    {
        $headers = function_exists('apache_request_headers')
            ? apache_request_headers()
            : $_SERVER;
        return isset($headers['REMOTE_ADDR']) ? $headers['REMOTE_ADDR'] : '0.0.0.0';
    }
    /**
     * 模拟提交参数，支持https提交 可用于各类api请求
     * @param string $url ： 提交的地址
     * @param array $data :POST数组
     * @param string $method : POST/GET，默认GET方式
     * @return mixed
     */
    public static function send($url, $data='', $method='GET'){
        if($method=='GET'){
            $param = '?';
            foreach ($data as $key => $value) {
                $param .= $key.'='.$value.'&';
            }
            $param = rtrim($param,'&');
            return file_get_contents($url.$param);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            exit(curl_error($ch));
        }
        curl_close($ch);
        # 返回结果集
        return $result;
    }
    /**
     * 判断是否SSL协议
     * @return boolean
     */
    public static function IS_SSL() {
        if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
            return true;
        }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
            return true;
        }
        return false;
    }
    /**
     * 是否为DELETE协议
     * @return bool
     */
    public static function IS_DELETE(){
        return self::REQUEST_METHOD() =='DELETE' ? true : false;
    }
    /**
     * 发送HTTP状态
     * @param integer $code 状态码
     * @return void
     */
    public static function send_http_status($code) {
        static $_status = array(
            # Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            # Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            # Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  # 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            # 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            # Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            # Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );

        if(isset($_status[$code])) {
            if(defined('IS_SWOOLE') && IS_SWOOLE===true){
                # SWOOLE 模式
                self::$response->status($code);
                # 结束请求
                self::$response->end('');
            }else{
                # 普通模式
                header('HTTP/1.1 '.$code.' '.$_status[$code]);
                # 确保FastCGI模式下正常
                header('Status:'.$code.' '.$_status[$code]);
            }
        }
    }
    /**
     * 判断是手机还是PC
     * true 为 手机  false 为PC
     */
    public static function IS_MOBILE(){
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
            $mobile_browser++;
        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
            $mobile_browser++;
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
            $mobile_browser++;
        if (isset($_SERVER['HTTP_PROFILE']))
            $mobile_browser++;
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
        );
        if (in_array($mobile_ua, $mobile_agents))
            $mobile_browser++;
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
            $mobile_browser++;
        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
            $mobile_browser = 0;
        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
            $mobile_browser++;
        if ($mobile_browser > 0)
            return true;
        else
            return false;
    }
}