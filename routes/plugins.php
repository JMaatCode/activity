<?php
/**
 * Created by PhpStorm.
 * User: jmaat
 * Date: 2018/8/28
 * Time: 9:35
 */

Route::group(['namespace' => '\\App\\Plugins'],function(){
    $dirroot = realpath(__DIR__.'/../app/PlugIns');
    $dir_handle = opendir($dirroot);
    while($dir = readdir($dir_handle)){
        if($dir != '.' && $dir != '..' && is_dir($dirroot.'/'.$dir)){
            $path = $dirroot.'/'.$dir.'/'.'route.php';
            if(file_exists($path)){
                include $path;
            }
        }
    }
});