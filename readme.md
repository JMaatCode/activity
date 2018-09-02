- �����չlaravel����

�������д���һ�����Ŀ�����ڶ̣����꼴����

������Ҫһ���ɲ��ʽ�����ļܹ���ʹ�ø�����Լ�����Ӱ�죬"���弴��"��

ѡ�ͣ���� laravel5.6���洢���ݿ� MongoDB���������ݽṹ��

ʵ�֣�
1. ��appĿ¼���½����Ŀ¼PlugIns���ڴ�Ŀ¼�£�ÿ���ļ�����һ�����
2. �������ģ��Test�������������ģ�忪����Ŀ¼�ṹ���£�
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
3. �ȴ�·�ɳ�����

routes/web.php ����plugins.php���·�ɣ���ȡÿ�����Ŀ�µ�·���ļ�route.php��
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
Testģ��·���ļ�route.php:
```
Route::get('test','\\App\\PlugIns\\Test\\Controllers\\TestController@test');
```
����http://my.activity.com/test ��TestController�������µ�Test������
4. ���ݿ�����

�ٴ�php��չ��֧��MongoDB 

������config/database.php�ļ�,��ȡÿ�����Ŀ�µ����ݿ������ļ�Config/database.php
```
$database = [];
//����plugin �����ݿ�����
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
Testģ�����ݿ������ļ�Config/database.php
```
//����������
$host = env('MONGO_DB_HOST','127.0.0.1');
if(strpos($host,',')){
    $host = explode(',',$host);
}
//��Ⱥ���Ӹ�������
$replicaSet = env('MONGO_DB_REPLICASET',false);
$options = [];
if($replicaSet){
    $options = [
        'replicaSet' => $replicaSet,
    ];
}
//�������ݿ����� Ϊÿ���������ͬ�����ݿ������������ò�ͬ�����ݿ⣨����test��
$database['connections']['test'] = [
    'driver' => 'mongodb',
    'host' => $host,
    'port' => env('MONGO_DB_PORT',27017),
    'database' => 'test',
    'options' => $options
];
```
���û���model��֧��MongoDB
```
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class BaseModel extends Eloquent
{
    protected $connection = 'mongodb';
    protected $guarded = [];
}
```
Testģ���Models�ļ��̳л���model����������
```
namespace App\PlugIns\Test\Models;
class BaseModel extends \App\Models\BaseModel
{
    protected $connection = 'test';
}
```
����TestModel���������ݱ�
```
namespace App\PlugIns\Test\Models;
class TestModel extends BaseModel
{
    protected $table = 'test';
}
```
�ڿ���������model
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
5. ����ģ���ļ�
��������PlugInsController�����л�Ŀ������̳л���������
```
namespace App\PlugIns;
use Illuminate\Support\Facades\View;
class PlugInsController
{
    protected $view_path;
    protected function view($view,$data = []){
        $path = $this->view_path . str_replace('.','/',$view) . '.blade.php';
        //View::getFacadeRoot() ����View����
        return View::getFacadeRoot()->file($path,$data);
    }
}
```
Testģ���µĿ������̳л���������
```
namespace App\PlugIns\Test\Controllers;
use App\PlugIns\PlugInsController;
use App\PlugIns\Test\Models\TestModel;
class TestController extends PlugInsController
{
    function __construct()
    {
        //����ģ���ļ����Ŀ¼
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
Testģ���µ���ͼViews/test/test.blade.php
```
echo '<pre>';
print_r($data);
```