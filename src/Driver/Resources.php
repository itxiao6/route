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
    protected static $file_type = [
        '.css'=>'text/css',
        '.js'=>'application/javascript',
    ];
    /**
     * 资源路由的文件夹
     * @var array
     */
    protected static $folder = [];

    /**
     * 设置资源路由
     * @param $folder
     */
    public static function set_folder($folder)
    {
        self::$folder = $folder;
    }

    /**
     * 获取资源路由
     * @return array
     */
    public static function get_folder()
    {
        return self::$folder;
    }
    /**
     * 设置文件的响应类型
     * @param $file_type
     * @param null $value
     */
    public static function set_file_type($file_type,$value = null)
    {
        if(is_array($file_type)){
            foreach ($file_type as $key=>$value){
                self::$file_type[$key] = $value;
            }
        }else{
            self::$file_type[$file_type] = $value;
        }
    }

    /**
     * 获取文件响应类型
     * @return array
     */
    public static function get_file_type()
    {
        return self::$file_type;
    }

    /**
     * 响应内容
     */
    public static function out()
    {
        global $content_type;
        # 获取url
        $url = Http::get_url();
        # 安全过滤
        self::Authcheck($url);
        # 获取目录名
        $folder = substr($url,0,strpos($url,'/'));
        # 设置协议头:内容类型
        header('Content-Type:'.self::$file_type[substr( $url , strrpos($url , '.'))]);
        # 替换目录名
        $url = preg_replace('!^'.$folder.'!','',$url);
        # 高效率
        if(isset(self::$folder[$folder]) && file_exists(self::$folder[$folder].$url)){
            exit(file_get_contents(self::$folder[$folder].$url));
        }
        # 遍历目录
        foreach(self::$folder as $key=>$item){
            if(file_exists(self::$folder[$key].'/'.$url)){
                exit(file_get_contents(self::$folder[$key].'/'.$url));
            }
        }
    }

    /**
     * 检查文件是否存在
     * @return bool
     */
    public static function check()
    {
        # 获取url
        $url = Http::get_url();
        # 安全过滤
        self::Authcheck($url);
        # 获取目录名
        $folder = substr($url,0,strpos($url,'/'));
        # 替换目录名
        $url = preg_replace('!^'.$folder.'!','',$url);
        # 高效率
        if(isset(self::$folder[$folder]) && file_exists(self::$folder[$folder].$url)){
            return true;
        }
        # 遍历目录
        foreach(self::$folder as $key=>$item){
            if(file_exists(self::$folder[$folder].$url)){
                return true;
            }
        }
        return false;
    }

    /**
     * 参数安全过滤
     * @param $url
     */
    private static function Authcheck($url)
    {
        if(strpos($url,'..') || strpos($url,'../') || strpos($url,'/..') || preg_match('!\.php$!',$url)){
            Http::send_http_status(404);
        }
    }
}