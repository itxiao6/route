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
    protected static $folder = [];

    public static function out()
    {

    }
    public static function check()
    {
        # 获取url
        $url = Http::get_url();
        # 获取目录名
        # 遍历目录

    }
}