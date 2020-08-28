<?php
/**
 * Created by PhpStorm.
 * User: cos800
 * Date: 2018/9/3
 * Time: 下午8:26
 */

namespace tt;


class str
{
    static function startsWith($str, $start) {
        return (substr($str, 0, strlen($start))===$start);

    }
    static function endsWith($str, $end) {
        return (substr($str, -strlen($end))===$end);
    }
}