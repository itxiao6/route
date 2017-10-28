<?php
namespace Itxiao6\Route\Driver;
use Kernel\Config;
/**
 * 域名绑定
 * Class Host
 * @package Itxiao6\Route\Driver
 */
class Host implements \Itxiao6\Route\Interfaces\Host
{
    /**
     * 获取域名绑定的模块
     * @param $host
     * @return bool
     */
    public static function get_app($host)
    {
        if($host == null || $host == ''){
            return false;
        }else{
            return Config::get('host',$host);
        }
    }
}