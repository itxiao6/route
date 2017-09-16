<?php
namespace Itxiao6\Route;
use Exception;
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
     * @var bool
     */
    protected static $driver = false;

    # 初始化路由
    public static function init()
    {
        # 获取请求的url
        self::$uri = Http::get_uri(self::$keyword);
        # 拆分数据
        self::explode_uri();
        # 判断驱动是否被实例化
        if(self::$driver == false){
            self::$driver = new Driver;
        }
        # 调用接口
        if(self::$driver -> check(self::$app,self::$controller,self::$action)){
            # 启动程序
            echo self::$driver -> start(self::$app,self::$controller,self::$action);
        }else if(Resources::check()){
            # 寻找资源路由
            echo  Resources::out();
            # 结束程序
            exit();
        }else{
            throw new \Exception('找不到路由'.self::$app.','.self::$controller.','.self::$action);
            # 抛异常找不到路由
        }
    }
    # 拆分uri
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
            default:
                # 什么都不设置
                break;
        }
    }
    public function set_driver($driver)
    {

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

    # 虚拟目录
    public static function abstract_path($uri)
    {
        # 判断url是否包含非法字符
        if(strpos($uri,'../') || strpos($uri,'.php')){
            exit();
        }
        # 处理虚拟目录
        foreach (C('all','abstract') as $key => $value) {
            # 判断是否存在此文件
            if($uri!='' && $uri!='/' && file_exists($value.$uri)){
                # 获取文件类型
                $fileType = substr(strrchr($value.$uri,"."),1);
                # 判断文件类型是否为css
                if($fileType == 'css'){
                    header('Content-Type:text/css;');
                    # 判断文件类型是否为js文件
                }else if($fileType=='js'){
                    header('Content-Type:text/js;');
                }else if($fileType=='png'){
                    header('Content-Type:image/png;');
                }else if($fileType=='jpg'){
                    header('Content-Type:text/jpeg;');
                }else if($fileType=='gif'){
                    header('Content-Type:text/gif;');
                }
                // 输入文件内容
                exit(file_get_contents($value.$uri));
            }
        }
    }
    # 解析路由
    public static function __init()
    {
        # 获取url
        $uri = Http::get_url(true);

        # 定义请求URL
        define('REQUEST_URI',Http::get_url(false));

        # 处理虚拟目录
        self::abstract_path($uri);

        # 过滤url
        $route_url = preg_replace('!\.php$|\.html$|\.jsp$|\.aspx$|\.asp$|\.htm$!i','',$uri);

        # 替换开头的/
        $route_url = preg_replace('!^/!','',$route_url);

        # 判断访问的是否为首页
        if($route_url != ''){
            # 解析URL
            $route = explode('/',$route_url);
        }else{
            # 定义路由信息为空
            $route = [];
        }
        # 判断当前域名是否有模块绑定
        if(!empty(C($_SERVER['HTTP_HOST'],'host'))){
            # 按照域名解析法
            # 判断参数是否大于等于3
            if(count($route) >= 3){
                # 解析多余参数(伪静态)
                for($i=3;$i <= count($route);$i++){
                    # 判断值是否存在
                    if(isset($route[$i+1])){
                        # 解析到GET里面
                        $_GET[$route[$i]] = $route[$i+1];
                        # 跳过已经取过的值
                        $i++;
                    }
                }
            }

            # 保证 $_REQUEST能够取到值
            $_REQUEST = array_merge($_POST,$_GET,$_COOKIE);
            # 过滤空参数
            foreach ($route as $key => $value){
                # 判断路由结果是否为空
                if($value==''){
                    # 删除此路由
                    unset($route[$key]);
                }
            }
            # 过滤空参数
            foreach ($route as $key => $value){
                # 判断路由结果是否为空
                if($value==''){
                    # 删除此路由
                    unset($route[$key]);
                }
            }

            # 判断ACTION是否为空
            if(count($route) == 1 && empty($route[1])){
                $route[1] = C('default_a_name','app');
            }
            # 判断控制器是否为空
            if(count($route) == 0 && empty($route[0])){
                # 加载默认控制器名称
                $route[0] = C('default_c_name','app');
                # 加载默认操作名
                $route[1] = C('default_a_name','app');
            }
            # 加载绑定的应用
            array_unshift($route,C($_SERVER['HTTP_HOST'],'host'));
        }else{
            # 判断参数是否大于等于4
            if(count($route) >= 4){
                # 解析多余参数(伪静态)
                for($i=3;$i <= count($route);$i++){
                    # 判断值是否存在
                    if(isset($route[$i+1])){
                        # 解析到GET里面
                        $_GET[$route[$i]] = $route[$i+1];
                        # 跳过已经取过的值
                        $i++;
                    }
                }
            }
            # 保证 $_REQUEST能够取到值
            $_REQUEST = array_merge($_POST,$_GET,$_COOKIE);
            # 过滤空参数
            foreach ($route as $key => $value){
                # 判断路由结果是否为空
                if($value==''){
                    # 删除此路由
                    unset($route[$key]);
                }
            }
            # 判断A是否为空
            if(count($route)==3 && $route[2]==''){
                $route[2] = C('default_a_name','app');
            }
            # 判断传了几个URL
            if(count($route)==2){
                # 加载部分配置
                $route[2] = C('default_a_name','app');

                # 判断CA是否为空
            }else if(count($route)==1){
                $route[1] = C('default_c_name','app');
                $route[2] = C('default_a_name','app');
                # 判断是否 M A C都为空
            }else if(count($route)==0){
                # 判断是否存在Host绑定
                if(empty(C($_SERVER['HTTP_HOST'],'host'))){
                    # 加载默认的模块
                    $route[0] = C('default_m_name','app');

                }else{
                    # 加载Host绑定的模块
                    $route[0] = C($_SERVER['HTTP_HOST'],'host');

                }
                # 加载部分配置
                $route[1] = C('default_c_name','app');
                $route[2] = C('default_a_name','app');
            }
        }


        # 定义应用名称
        define('APP_NAME',$route[0]);

        # 定义控制器名称
        define('CONTROLLER_NAME',$route[1]);

        # 定义操作名称
        define('ACTION_NAME',$route[2]);
        # 判断是否为编辑器上传文件
        if(APP_NAME=='__System__' && CONTROLLER_NAME=='__System__' && ACTION_NAME=='__upload__'){
            self::upload();
        }
        # 判断模块是否存在
        if(!file_exists(ROOT_PATH.'app'.DIRECTORY_SEPARATOR.APP_NAME)){
            # 指定模块找不到
            throw new Exception('找不到 '.APP_NAME.' 模块');
        }

        # 定义应用视图模板路径
        C('view_path','sys',['app' => ROOT_PATH.'app'.DIRECTORY_SEPARATOR.$route[0].DIRECTORY_SEPARATOR.'View','message'=>ROOT_PATH.'message']);
        # 获取类名
        $className = 'App\\'.$route[0].'\Controller\\'.CONTROLLER_NAME;

        # 获取操作名
        $actionNane = $route[2];

        # 判断控制器文件是否存在
        if(file_exists(ROOT_PATH.'app'.DIRECTORY_SEPARATOR.APP_NAME.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.CONTROLLER_NAME.'.php') && class_exists($className)){
            # 实例化控制器
            $controller = new $className;

            # 定义display和魔术方法列表(不能作为操作名)
            $magic = ['__init','__construct','display','__destruct','__call','__callStatic','__get','__set','__isset','__unset','__sleep','__wakeup','__toString','__invoke','__set_state','__clone','__debugInfo'];

            # 判断控制器内操作是否存在
            if(method_exists($controller,$actionNane) && (!in_array($actionNane,$magic))){
                if(C('debugbar','sys')) {
                    global $debugbar;
                    global $debugbarRenderer;
                    # SESSION 信息
                    $debugbar['Request'] -> addMessage(['SESSION'=>$_SESSION]);
                    # GET 信息
                    $debugbar['Request'] -> addMessage(['GET'=>$_GET]);
                    # POST 信息
                    $debugbar['Request'] -> addMessage(['POST'=>$_POST]);
                    # COOKIE 信息
                    $debugbar['Request'] -> addMessage(['COOKIE'=>$_COOKIE]);
                    # SERVER 信息
                    $debugbar['Request'] -> addMessage(['SERVER'=>$_SERVER]);
                    # 应用名
                    $debugbar['Application'] -> addMessage('应用名:'.APP_NAME);
                    # 控制器
                    $debugbar['Application'] -> addMessage('模块名:'.CONTROLLER_NAME);
                    # 操作
                    $debugbar['Application'] -> addMessage('操作名'.ACTION_NAME);
                }
                # 实例化控制器
                $controller -> $actionNane();

            }else{
                # 判断是否 存在此模板
                if(file_exists(ROOT_PATH.'app'.DIRECTORY_SEPARATOR.APP_NAME.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$actionNane.'.html')){
                    # 渲染模板
                    $controller -> display();
                    exit();
                }

                # 指定操作名找不到
                throw new Exception('找不到 '.$actionNane.' 操作');
            }
        }else{
            # 判断是否 存在此模板
            if(file_exists(ROOT_PATH.'app'.DIRECTORY_SEPARATOR.APP_NAME.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$actionNane.'.html')
                || file_exists(ROOT_PATH.'app'.DIRECTORY_SEPARATOR.APP_NAME.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$actionNane.'.php')
                || file_exists(ROOT_PATH.'app'.DIRECTORY_SEPARATOR.APP_NAME.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$actionNane.'.tpl')
            ){
                # 实例化控制器父类
                $controller = new Controller;
                # 渲染模板
                $controller -> display();
            }else{
                # 指定控制器找不到
                throw new Exception('找不到 '.CONTROLLER_NAME.' 控制器');
            }
        }
    }
    public static function upload(){
        include ROOT_PATH."common/umeditor/php/Uploader.class.php";
        //上传配置
        $config = array(
            "savePath" => "/upload/umeditor/",             //存储文件夹
            "maxSize" => 1000 ,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
        );
        // 上传文件保存目录
        $Path = ROOT_PATH."public/upload/umeditor/";
        // 背景保存在临时目录中
        $config[ "savePath" ] = $Path;
        $up = new Uploader( "upfile" , $config );
        $type = $_REQUEST['type'];
        $callback=$_GET['callback'];

        $info = $up->getFileInfo();
        /**
         * 返回数据
         */
        if($callback) {
            exit('<script>'.$callback.'('.json_encode($info).')</script>');
        } else {
            exit(json_encode($info));
        }
    }
}