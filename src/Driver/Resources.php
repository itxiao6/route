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
     * 资源路由的文件夹
     * @var array
     */
    protected $folder = [];

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
    public function out()
    {
        # 判断是否为Swoole 模板
        if(defined('IS_SWOOLE') && IS_SWOOLE===true){
            # 获取swoole url信息
            $url = Http::$request -> server['request_uri'];
        }else{
            # 获取url
            $url = Http::get_url();
        }
        # 安全过滤
        $this -> Authcheck($url);
        # 获取目录名
        $folder = substr($url,0,strpos($url,'/'));
        # 设置协议头:内容类型
        header('Content-Type:'.$this -> file_type[substr( $url , strrpos($url , '.'))]);
        # 替换目录名
        $url = preg_replace('!^'.$folder.'!','',$url);
        # 高效率
        if(isset($this -> folder[$folder]) && file_exists($this -> folder[$folder].$url)){
            exit(file_get_contents($this -> folder[$folder].$url));
        }
        # 遍历目录
        foreach($this -> folder as $key=>$item){
            if(file_exists($this -> folder[$key].'/'.$url)){
                exit(file_get_contents($this -> folder[$key].'/'.$url));
            }
        }
    }

    /**
     * 检查文件是否存在
     * @return bool
     */
    public function check()
    {
        # 判断是否为Swoole 模板
        if(defined('IS_SWOOLE') && IS_SWOOLE===true){
            # 获取swoole url信息
            $url = Http::$request -> server['request_uri'];
        }else{
            # 获取url
            $url = Http::get_url();
        }
        # 安全过滤
        $this -> Authcheck($url);
        # 获取目录名
        $folder = substr($url,0,strpos($url,'/'));
        # 替换目录名
        $url = preg_replace('!^'.$folder.'!','',$url);
        # 高效率
        if(isset($this -> folder[$folder]) && file_exists($this -> folder[$folder].$url)){
            return true;
        }
        # 遍历目录
        foreach($this -> folder as $key=>$item){
            if(file_exists($this -> folder[$folder].$url)){
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