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
    protected $defaultSoftDelete = 0;

//    protected $autoWriteTimestamp = true;
//    protected $createTime = 'create_time';
//    protected $updateTime = 'update_time';

//    function getCreateTimeAttr($value) {
//        $format = Config::get('database.datetime_format');
//        return $value ? date($format, $value) : '';
//    }
//
//    function getUpdateTimeAttr($value) {
//        $format = Config::get('database.datetime_format');
//        return $value ? date($format, $value) : '';
//    }

    // 生成 <option> HTML
    static function _optionsHtml($arr) {
        $html = '';
        foreach ($arr as $k=>$v) {
            $html .= "<option value=\"$k\">$v</option>";
        }
        return $html;
    }
    // 获取 options 里定义的选项
    static function _optionsArr($field) {
        return static::$options[$field] ?: [];
    }
    static $options = [
//        'display_mode' => [
//            '' => '默认(一行四列)',
//            'swiper' => '轮播',
//        ]
    ];
    static function optionsHtml($field) {
        return static::_optionsHtml(static::_optionsArr($field));
    }

    static function optionsText($field, $key) {
        $arr = static::_optionsArr($field);
        return $arr[$key] ?: '';
    }

    // 关联... 比较难释解
    static $ttIds = [];
    static $ttAll = [];

    static function ttJoin($all, $field) {
        if (empty($all)) return;

        foreach ($all as $row) {
            $value = $row[$field];
            if (empty($value)) continue;

            if (!is_array($value))
                $value = explode(',', $value);

            static::$ttIds = array_merge(static::$ttIds, $value);
        }
    }

    static function ttGet($id) {
        if (empty(static::$ttAll)) {
            if (empty(static::$ttIds)) throw new \Exception('ttIds is empty');

            static::$ttIds = array_unique(static::$ttIds);

            $all = static::whereIn('id', static::$ttIds)->select();

            static::$ttAll = array_combine($all->column('id'), $all->all());
        }

        return static::$ttAll[$id];
    }
}

/*
ALTER TABLE `game`
ADD `create_time` INT UNSIGNED NOT NULL DEFAULT '0',
ADD `update_time` INT UNSIGNED NOT NULL DEFAULT '0',
ADD `delete_time` INT UNSIGNED NOT NULL DEFAULT '0';
 */