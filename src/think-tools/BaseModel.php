<?php
/**
 * Created by PhpStorm.
 * User: cos800
 * Date: 2018/4/26
 * Time: 下午5:54
 */

namespace tt;


use think\facade\Config;
use think\Model;
use think\model\concern\SoftDelete;


class BaseModel extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    function getCreateTimeAttr($value) {
        $format = Config::get('database.datetime_format');
        return $value ? date($format, $value) : '';
    }

    function getUpdateTimeAttr($value) {
        $format = Config::get('database.datetime_format');
        return $value ? date($format, $value) : '';
    }
}