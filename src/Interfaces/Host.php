<?php
namespace Itxiao6\Route\Interfaces;
/**
 * 域名绑定驱动接口
 * Interface Host
 * @package Itxiao6\Route\Interfaces
 */
interface Host
{
    /**
     * 获取域名绑定的模块
     * @param $host
     * @return bool
     */
    public static function get_app($host);
}