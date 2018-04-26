<?php
/**
 * Created by PhpStorm.
 * User: cos800
 * Date: 2018/4/26
 * Time: 下午5:54
 */

namespace tt;


use think\Model;
use think\model\concern\SoftDelete;


class BaseModel extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}