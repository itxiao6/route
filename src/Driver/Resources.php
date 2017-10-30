<?php
namespace Itxiao6\Route\Driver;
use Itxiao6\Route\Bridge\Http;

/**
 * 资源路由
 * Class Resources
 * @package Itxiao6\Route\Driver
 */
class Resources implements \Itxiao6\Route\Interfaces\Resources
{
    protected $file_type = [
        '.css'=>'text/css',
        '.js'=>'application/javascript',
    ];
    /**
     * 已经读取过的文件内容
     * @var array
     */
    protected static $file = [];
    /**
     * 资源路由的文件夹
     * @var array
     */
    protected $folder = [];

    /**
     * 当前文件地址
     * @var string
     */
    protected $file_path = '';
    /**
     * 设置资源路由
     * @param $folder
     * @return $this
     */
    public function set_folder($folder)
    {
        $this -> folder = $folder;
        return $this;
    }

    /**
     * 获取资源路由
     * @return array
     */
    public function get_folder()
    {
        return $this -> folder;
    }

    /**
     * 设置文件的响应类型
     * @param $file_type
     * @param null $value
     * @return $this
     */
    public function set_file_type($file_type,$value = null)
    {
        if(is_array($file_type)){
            foreach ($file_type as $key=>$value){
                $this -> file_type[$key] = $value;
            }
        }else{
            $this -> file_type[$file_type] = $value;
        }
        return $this;
    }

    /**
     * 获取文件响应类型
     * @return array
     */
    public function get_file_type()
    {
        return $this -> file_type;
    }

    /**
     * 响应内容
     */
    public function output()
    {
        # 判断文件是否存在
        if(file_exists($this -> file_path)){
            # 获取文件类型
            $file_type = $this -> file_type['.'.$this -> getExt($this -> file_path)['extension']];
            # 判断文件是否已经读取过了
            if(isset(self::$file[$this -> file_path])){
                # 响应内容
                Http::output(self::$file[$this -> file_path],$file_type);
            }else{
                # 响应内容
                Http::output(file_get_contents($this -> file_path),$file_type);
            }
        }else{
            Http::output('');
        }
    }

    /**
     * 获取文件后缀
     * @param $url
     * @return mixed
     */
    protected function getExt($url){
        return pathinfo($url);
    }

    /**
     * 检查文件是否存在
     * @return bool
     */
    public function check()
    {
        # 判断是否为Swoole 模式
        if(defined('IS_SWOOLE') && IS_SWOOLE===true){
            # 获取swoole url信息
            $url = Http::$request -> server['request_uri'];
        }else{
            # 获取url
            $url = $_SERVER['REQUEST_URI'];
        }
        # 安全过滤
        $this -> Authcheck($url);
        # 替换开头的 /
        $url = preg_replace('!^\/!','',$url);
        # 获取目录名
        $folder = substr($url,0,strpos($url,'/'));
        # 判断资源路由目录是否定义过
        if(isset($this -> folder[$folder])){
            $url = preg_replace('!^'.$folder.'!','',$url);
            # 高效率
            if(file_exists($this -> folder[$folder].$url)){
                $this -> file_path = $this -> folder[$folder].$url;
                return true;
            }
        }
        # 遍历目录
        foreach($this -> folder as $key=>$item){
            if(file_exists($this -> folder[$folder].$url)){
                $this -> file_path = $this -> folder[$folder].$url;
                return true;
            }else if(file_exists($this -> folder.$url)){
                $this -> file_path = $this -> folder.$url;
                return true;
            }
        }
        return false;
    }

    /**
     * 参数安全过滤
     * @param $url
     */
    private function Authcheck($url)
    {
        if(strpos($url,'..') || strpos($url,'../') || strpos($url,'/..') || preg_match('!\.php$!',$url)){
            Http::send_http_status(404);
        }
    }
}