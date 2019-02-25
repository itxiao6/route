<?php
namespace Itxiao6\Route;

/**
 * 路由类
 * Class Route
 * @package Itxiao6\Route
 */
class Route{
    /**
     * 请求
     * @var mixed
     */
    protected $request = null;
    /**
     * 路由配置
     * @var array
     */
    protected $config = [
        'keyword'=>'/',
        'default_app'=>'Home',
        'default_controller'=>'Index',
        'default_action'=>'index',
    ];

    /**
     * @param null|\Closure $callback
     * @return mixed
     */
    public function start($callback = null)
    {
        $mvc = $this -> explode_uri($this -> request -> RawRequest() -> server['request_uri']);
        return $callback($mvc['app'],$mvc['controller'],$mvc['action']);
    }
    /**
     * 拆分URL
     * @param $uri
     * @return array
     */
    protected function explode_uri($uri)
    {
        $app = $this -> config('default_app');
        $controller = $this -> config('default_controller');
        $action = $this -> config('default_action');
        # 判断是否为首页
        if($uri != '' && $uri != '/'){
            # 拆分uri
            $result = explode($this -> config('keyword'),$uri);
        }else{
            # 定义uri
            $result = [];
        }
        # 解析参数
        switch(count($result)){
            case 3:
                # 应用名
                $app = $result[0];
                # 控制器名
                $controller = $result[1];
                # 操作名
                $action = $result[2];
                break;
            case 2:
                # 设置应用名
                $app = $result[0];
                # 设置控制器名
                $controller = $result[1];
                break;
            case 1:
                # 只设置应用名
                $app = $result[0];
                break;
            case 0:
                break;
            default:
                if(count($result) > 3){
                    # 应用名
                    $app = $result[0];
                    # 控制器名
                    $controller = $result[1];
                    # 操作名
                    $action = $result[2];
                }
                # 什么都不设置
                break;
        }
        return ['app'=>$app,'controller'=>$controller,'action'=>$action];
    }

    /**
     * 修改配置
     * @param null|string|array $key
     * @param null|mixed $value
     * @return $this
     */
    public function config($key=null,$value = null)
    {
        if($key === null && $value === null){
            return $this -> config;
        }
        if(is_array($key) && count($key) > 0 && $value === null){
            $this -> config = $key;
        }
        if(is_string($key) && $value != null){
            $this -> config[$key] = $value;
        }
        if(is_string($key) && $value == null){
            return isset($this -> config[$key])?$this -> config[$key]:null;
        }
        return $this;
    }

    /**
     * 获取接口
     * @param $request
     * @return Route
     */
    public static function getInterface($request)
    {
        return new static($request);
    }

    /**
     * 实例化路由
     * Route constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this -> request = $request;
    }

    /**
     * 修饰者模式
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return (new static()) -> $name(...$arguments);
    }
}