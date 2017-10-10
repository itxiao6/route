<?php
namespace Itxiao6\Route;
use Kernel\Config;
/**
 * 域名绑定
 * Class Host
 * @package Itxiao6\Route
 */
class Host
{
    /**
     * 获取域名绑定的模块
     * @param $host
     * @return bool
     */
    public static function get_app($host)
    {
        return Config::get('host',$host);
    }
}