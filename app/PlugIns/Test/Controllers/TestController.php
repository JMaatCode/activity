<?php
/**
 * Created by PhpStorm.
 * User: jmaat
 * Date: 2018/8/28
 * Time: 10:31
 */

namespace App\PlugIns\Test\Controllers;


use App\PlugIns\PlugInsController;
use App\PlugIns\Test\Models\TestModel;

class TestController extends PlugInsController
{
    function __construct()
    {
        $this->view_path = dirname(__FILE__) . '/../Views/test/';
    }

    public function Test(){
        /*$testModel = new TestModel();
        $testModel->create(['id'=>1,'name'=>'test']);*/

        $data = TestModel::get()->toArray();
        return $this->view('test',['data'=>$data]);
    }
}