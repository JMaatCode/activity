- 搭建可扩展laravel活动框架

背景：有大量一批活动项目，周期短，用完即弃。

需求：需要一个可插件式开发的架构，使得各个活动自己互不影响，"即插即拔"。

选型：框架 laravel5.6、存储数据库 MongoDB（灵活的数据结构）

实现：
1. 在app目录下新建插件目录PlugIns，在此目录下，每个文件代表一个活动。
2. 创建插件模板Test，后续插件复制模板开发。目录结构如下：
```
Test{
    Config:{
        database.php
    },
    Controllers:{
        TestController.php
    },
    Models:{
        BaseModel.php,
        TestModel.php
    },
    Views:{
        test:{
            test.blade.php
        }
    },
    route.php
}
PlugInsController.php
```
3. 先从路由出发：

routes/web.php 引入plugins.php插件路由，读取每个活动项目下的路由文件route.php：
```
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
```
Test模板路由文件route.php:
```
Route::get('test','\\App\\PlugIns\\Test\\Controllers\\TestController@test');
```
访问http://my.activity.com/test 到TestController控制器下的Test方法。
4. 数据库配置

①打开php扩展，支持MongoDB 

②配置config/database.php文件,读取每个活动项目下的数据库配置文件Config/database.php
```
$database = [];
//引入plugin 的数据库配置
$dirroot = realpath(__DIR__.'/../app/PlugIns');
$dir_handle = opendir($dirroot);
while ($dir = readdir($dir_handle)){
    if($dir != '.' && $dir != '..' && is_dir($dirroot.'/'.$dir)){
        $path = $dirroot.'/'.$dir.'/Config/database.php';
        if(file_exists($path)){
            include $path;
        }
    }
}

return $database;
```
Test模板数据库配置文件Config/database.php
```
//服务器配置
$host = env('MONGO_DB_HOST','127.0.0.1');
if(strpos($host,',')){
    $host = explode(',',$host);
}
//集群主从复制配置
$replicaSet = env('MONGO_DB_REPLICASET',false);
$options = [];
if($replicaSet){
    $options = [
        'replicaSet' => $replicaSet,
    ];
}
//加入数据库配置 为每个活动创建不同的数据库连接名，配置不同的数据库（例：test）
$database['connections']['test'] = [
    'driver' => 'mongodb',
    'host' => $host,
    'port' => env('MONGO_DB_PORT',27017),
    'database' => 'test',
    'options' => $options
];
```
配置基础model，支持MongoDB
```
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class BaseModel extends Eloquent
{
    protected $connection = 'mongodb';
    protected $guarded = [];
}
```
Test模板的Models文件继承基础model，配置连接
```
namespace App\PlugIns\Test\Models;
class BaseModel extends \App\Models\BaseModel
{
    protected $connection = 'test';
}
```
创建TestModel，配置数据表
```
namespace App\PlugIns\Test\Models;
class TestModel extends BaseModel
{
    protected $table = 'test';
}
```
在控制器调用model
```
namespace App\PlugIns\Test\Controllers;
use App\PlugIns\Test\Models\TestModel;
class TestController
{
    public function Test(){
        $testModel = new TestModel();
        $testModel->create(['id'=>1,'name'=>'test']);
        $data = TestModel::get()->toArray();
        dd($data);
    }
}
```
5. 引入模板文件
创建基础PlugInsController，所有活动的控制器继承基础控制器
```
namespace App\PlugIns;
use Illuminate\Support\Facades\View;
class PlugInsController
{
    protected $view_path;
    protected function view($view,$data = []){
        $path = $this->view_path . str_replace('.','/',$view) . '.blade.php';
        //View::getFacadeRoot() 返回View对象
        return View::getFacadeRoot()->file($path,$data);
    }
}
```
Test模板下的控制器继承基础控制器
```
namespace App\PlugIns\Test\Controllers;
use App\PlugIns\PlugInsController;
use App\PlugIns\Test\Models\TestModel;
class TestController extends PlugInsController
{
    function __construct()
    {
        //配置模板文件存放目录
        $this->view_path = dirname(__FILE__) . '/../Views/test/';
    }

    public function Test(){
        $testModel = new TestModel();
        $testModel->create(['id'=>1,'name'=>'test']);
        $data = TestModel::get()->toArray();
        return $this->view('test',['data'=>$data]);
    }
}
```
Test模板下的视图Views/test/test.blade.php
```
echo '<pre>';
print_r($data);
```