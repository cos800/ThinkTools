<?php
/**
 * User: cos800
 * Date: 2018/5/4
 * Time: 下午2:45
 */

namespace tt;

use think\facade\Request;

class UploadController extends ApiBaseController
{
    function index() {
        $subdir = 'temp';
        $rootPath = config('app.upload.root_path');
//        if (!file_exists($rootPath.$subdir)) {
//            mkdir($rootPath.$subdir, 0777, true);
//        }


        $file = request()->file('file');
        $info = $file->validate([
            'size' => config('app.upload.size'),
            'ext' => config('app.upload.ext'),
        ])->rule(function () {
            return date('ym/d-His-').substr(microtime(),2,8);
        })->move($rootPath.$subdir);

        if ($info) {
            $path = $subdir.'/'.$info->getSaveName();

            $data = [
                'path' => $path,
                'url' => \tt::up($path),
            ];

            \tt::success($data);
        }else{
            \tt::error($file->getError());
        }
    }
}