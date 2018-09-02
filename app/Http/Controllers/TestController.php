<?php
/**
 * Created by PhpStorm.
 * User: jmaat
 * Date: 2018/8/28
 * Time: 22:32
 */

namespace App\Http\Controllers;


class TestController extends Controller
{
    public function Test(){
        return view('test.test');
    }
}