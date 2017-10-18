<?php
namespace Itxiao6\Route\Interfaces;
/**
 * 资源路由接口
 * Interface Resources
 * @package Itxiao6\Route\Interfaces
 */
interface Resources
{
    /**
     * 检查文件是否存在
     * @return bool
     */
    public static function check();
    /**
     * 响应内容
     */
    public static function out();
    /**
     * 获取文件响应类型
     * @return array
     */
    public static function get_file_type();
    /**
     * 设置文件的响应类型
     * @param $file_type
     * @param null $value
     */
    public static function set_file_type($file_type,$value = null);
    /**
     * 获取资源路由
     * @return array
     */
    public static function get_folder();
    /**
     * 设置资源路由
     * @param $folder
     */
    public static function set_folder($folder);


}