<?php
/**
 * Created by PhpStorm.
 * User: jmaat
 * Date: 2018/8/28
 * Time: 21:12
 */

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
//加入数据库配置
$database['connections']['test'] = [
    'driver' => 'mongodb',
    'host' => $host,
    'port' => env('MONGO_DB_PORT',27017),
    'database' => 'test',
    'options' => $options
];