<?php
namespace Itxiao6\Route;
/**
 * MVC 启动接口
 * Interface IDriver
 * @package Itxiao6\Route
 */
interface IDriver
{
    public function check($app,$controller,$action);
    public function start($app,$controller,$action);
}