<?php
namespace Itxiao6\Route;
/**
 * 资源路由
 * Class Resources
 * @package Itxiao6\Route
 */
class Resources
{
    /**
     * 资源路由的文件夹
     * @var array
     */
    protected static $folder = [
        'js'=>__DIR__.'/../test/js',
    ];

    public static function out()
    {
        # 获取url
        $url = Http::get_url();
        # 安全过滤
        self::Authcheck($url);
        # 获取目录名
        $folder = substr($url,0,strpos($url,'/'));
        # 高效率
        if(isset(self::$folder[$folder]) && file_exists(self::$folder[$folder].'/'.$url)){
            return file_get_contents(self::$folder[$folder].'/'.$url);
        }
        # 遍历目录
        foreach(self::$folder as $key=>$item){
            if(file_exists(self::$folder[$key].'/'.$url)){
                return file_get_contents(self::$folder[$key].'/'.$url);
            }
        }
    }
    public static function check()
    {
        # 获取url
        $url = Http::get_url();
        # 安全过滤
        self::Authcheck($url);
        # 获取目录名
        $folder = substr($url,0,strpos($url,'/'));
        # 高效率
        if(isset(self::$folder[$folder]) && file_exists(self::$folder[$folder].'/'.$url)){
            return file_get_contents(self::$folder[$folder].'/'.$url);
        }
        # 遍历目录
        foreach(self::$folder as $key=>$item){
            if(file_exists(self::$folder[$key].'/'.$url)){
                return file_get_contents(self::$folder[$key].'/'.$url);
            }
        }
    }
    # 安全过滤
    public static function Authcheck($url)
    {
        if(strpos($url,'..') || strpos($url,'../') || strpos($url,'/..') || preg_match('!\.php$!',$url)){
            Http::send_http_status(404);
        }
    }
}