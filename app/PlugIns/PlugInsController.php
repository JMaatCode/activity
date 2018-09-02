<?php
/**
 * Created by PhpStorm.
 * User: jmaat
 * Date: 2018/8/28
 * Time: 22:42
 */

namespace App\PlugIns;


use Illuminate\Support\Facades\View;

class PlugInsController
{
    protected $view_path;
    protected function view($view,$data = []){
        $path = $this->view_path . str_replace('.','/',$view) . '.blade.php';
        return View::getFacadeRoot()->file($path,$data);
    }
}