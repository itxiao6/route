<?php
namespace Itxiao6\Route;
use ReflectionClass;
use ReflectionMethod;
use Reflection;
/**
 * MVC默认驱动
 * Class Driver
 * @package Itxiao6\Route
 */
class Driver implements IDriver
{
    # 启动MVC
    public function start($app,$controller,$action)
    {
        # 组合类名
        $controller_class = '\App\\'.$app.'\\Controller\\'.$controller;
        # 实例化控制器
        $controller = new $controller_class;
        # 调用方法
        return $controller -> $action();
    }
    # 检查方法是否存在
    public function check($app,$controller,$action)
    {
        # 组合类名
        $controller_class = '\App\\'.$app.'\\Controller\\'.$controller;
        # 判断类是否存在
        if(!class_exists($controller_class)){
            return false;
        }
        # 获取控制器的映射
        $result = new ReflectionClass($controller_class);
        # 获取控制器下的方法
        $methods = $result -> getMethods();
        # 定义可用的操作
        $actions = [];
        # 循环映射结果
        foreach ($methods as $key=>$value){
            # 获取方法映射
            $obj = new ReflectionMethod($value->class,$value -> name);
            # 获取权限
            $obj = Reflection::getModifierNames($obj->getModifiers());
            # 判断是否为公用方法 并且是静态方法
            if($obj[0] == 'public'
//                && $obj[1] == 'static'
            ){
                $actions[] = $value -> name;
            }
        }
        # 判断是否有效
        if(in_array($action,$actions)){
            return true;
        }else{
            return false;
        }
    }
}