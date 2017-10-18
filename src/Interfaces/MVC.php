<?php
namespace Itxiao6\Route\Interfaces;
/**
 * MVC 启动接口
 * Interface MVC
 * @package Itxiao6\Route\Interfaces
 */
interface MVC
{
    public function check($app,$controller,$action);
    public function start($app,$controller,$action);
}