<?php
/**
 * User: cos800
 * Date: 2018/4/25
 * Time: 下午4:07
 */

namespace tt;


class ApiBaseController
{
    function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
    }


}