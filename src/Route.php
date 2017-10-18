<?php
namespace Itxiao6\Route;
use Exception;
use Itxiao6\Route\Bridge\Http;
use Itxiao6\Route\Driver\Host;
use Itxiao6\Route\Driver\MVC;
use Itxiao6\Route\Driver\Resources;

# 路由类
class Route{
    /**
     * 路由
     * @var string
     */
    protected static $uri = '';
    /**
     * 模块
     * @var string
     */
    protected static $app = 'Home';
    /**
     * 控制器
     * @var string
     */
    protected static $controller = 'Index';
    /**
     * 操作
     * @var string
     */
    protected static $action = 'index';
    /**
     * 默认用来拆分路由的关键字
     * @var string
     */
    public static $keyword = '/';

    /**
     * MAC驱动
     * @var string
     */
    protected static $mvc_driver = MVC::class;

    /**
     * 资源路由驱动
     * @var string
     */
    protected static $resources_driver = Resources::class;

    /**
     * 初始化路由
     * @param \Closure|null $callback
     * @return mixed
     * @throws Exception
     */
    public static function init(\Closure $callback = null)
    {
        # 获取请求的url
        self::$uri = Http::get_uri(self::$keyword);
        # 拆分数据
        self::explode_uri();
        # 判断MVC驱动是否被实例化
        if(!is_object(self::$mvc_driver)){
            self::$mvc_driver = new self::$mvc_driver;
        }
        # 判断资源路由驱动是否被实例化
        if(!is_object(self::$resources_driver)){
            self::$resources_driver = new self::$resources_driver;
        }
        # 调用回调
        if($callback!=null){
            $callback(self::$app,self::$controller,self::$action);
        }
        # 调用接口
        if(self::$mvc_driver -> check(self::$app,self::$controller,self::$action)){
            # 启动程序
            return self::$mvc_driver -> start(self::$app,self::$controller,self::$action);
        }else if(self::$resources_driver -> check()){
            # 响应资源
            self::$resources_driver -> out();
        }else{
            throw new \Exception('找不到路由'.self::$app.','.self::$controller.','.self::$action);
            # 抛异常找不到路由
        }
    }

    /**
     * 拆分URL
     */
    public static function explode_uri()
    {
        # 判断是否为首页
        if(self::$uri!=''){
            # 拆分uri
            $result = explode(self::$keyword,self::$uri);
        }else{
            # 定义uri
            $result = [];
        }
        # 判断是否存在域名绑定
        if($app = Host::get_app($_SERVER['HTTP_HOST'])){
            # 插入应用名
            array_unshift($result,$app);
        }
        # 解析参数
        switch(count($result)){
            case 3:
                # 应用名
                self::set_default_app($result[0]);
                # 控制器名
                self::set_default_controller($result[1]);
                # 操作名
                self::set_default_action($result[2]);
                break;
            case 2:
                # 设置应用名
                self::set_default_app($result[0]);
                # 设置控制器名
                self::set_default_controller($result[1]);
                break;
            case 1:
                # 只设置应用名
                self::set_default_app($result[0]);
                break;
            case 0:
                # 什么都不设置
                break;
            default:
                if(count($result) > 3){
                    # 应用名
                    self::set_default_app($result[0]);
                    # 控制器名
                    self::set_default_controller($result[1]);
                    # 操作名
                    self::set_default_action($result[2]);
                }
                # 什么都不设置
                break;
        }
    }

    /**
     * 设置自定义MVC驱动
     * @param $driver
     */
    public function set_driver($driver)
    {
        self::$driver = $driver;
    }

    /**
     * 获取MVC自定义驱动
     * @return bool
     */
    public function get_driver()
    {
        return self::$driver;
    }

    /**
     * 设置资源路由驱动
     * @param string | object $class
     */
    public static function set_resources_driver($class)
    {
        self::$resources_driver = $class;
    }

    /**
     * 获取资源路由
     * @return string | object
     */
    public static function get_resources_driver()
    {
        return self::$resources_driver;
    }

    /**
     * 设置url分隔符
     * @param string $key_word
     */
    public static function set_key_word($key_word = '/')
    {
        self::$keyword = $key_word;
    }

    /**
     * 获取默认的url 分隔符
     * @return string
     */
    public static function get_key_word()
    {
        return self::$keyword;
    }
    /**
     * 设置默认的控制器
     * @param $name
     */
    public static function set_default_controller($name)
    {
        self::$controller = $name;
    }

    /**
     * 获取默认的控制器
     * @return string
     */
    public static function get_default_controller()
    {
        return self::$controller;
    }

    /**
     * 设置默认的操作名
     * @param $name
     */
    public static function set_default_action($name)
    {
        self::$action = $name;
    }

    /**
     * 获取默认的操作名
     * @return string
     */
    public static function get_default_action()
    {
        return self::$action;
    }

    /**
     * 设置默认的模块名
     * @param $name
     */
    public static function set_default_app($name)
    {
        self::$app = $name;
    }

    /**
     * 获取默认的模块名
     * @return string
     */
    public static function get_default_app()
    {
        return self::$app;
    }
}