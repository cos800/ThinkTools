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

    function success($data=[], $msg='') {
        $this->json([
            'ok' => 1,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    function error($msg, $data=[]) {
        $this->json([
            'ok' => 0,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    function json($arr) {
        header('Content-type: application/json');
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
        exit;
    }
}