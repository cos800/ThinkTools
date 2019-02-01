<?php
/**
 * Created by PhpStorm.
 * User: cos800
 * Date: 2019/2/1
 * Time: 4:14 PM
 */

namespace tt;


use think\facade\Request;
use think\facade\Session;

class Token
{
    static $name = '__token__';
    static $limit = 10;

    static protected function getList() {
        return Session::get(static::$name)?:[];
    }

    static protected function setList($list) {
        return Session::set(static::$name, $list);
    }

    static function create() {
        $token = md5($_SERVER['REQUEST_TIME_FLOAT']);

        $list = static::getList();
        array_unshift($list, $token);
        $list = array_slice($list, 0, static::$limit);
        static::setList($list);

        return '<input type="hidden" name="__token__" value="' . $token . '" />';
    }

    static function valid() {
        $token = Request::param(static::$name);
        $list = static::getList();
        return in_array($token, $list);
    }

    static function remove() {
        $token = Request::param(static::$name);
        $list = static::getList();
        $idx = array_search($token, $list);
        if ($idx===false) {
            throw new \Exception('无效令牌');
        }
        unset($list[$idx]);
        static::setList($list);
    }
}