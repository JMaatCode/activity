<?php
/**
 * Created by PhpStorm.
 * User: jmaat
 * Date: 2018/8/28
 * Time: 9:21
 */

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class BaseModel extends Eloquent
{
    protected $connection = 'mongodb';
    protected $guarded = [];
}