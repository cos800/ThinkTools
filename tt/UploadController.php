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
    public $tempDir = 'temp/';

    function index() {
        $rootPath = config('app.upload.root_path');

        $file = request()->file('file');

        $info = $file->validate([
            'size' => config('app.upload.size'),
            'ext' => config('app.upload.ext'),
        ])->rule(function () {
            return $this->tempDir.date('ym/d-His-').substr(microtime(),2,8);
        })->move($rootPath);

        if ($info) {
            $path = $info->getSaveName();

            $data = [
                'path' => $path,
                'url' => \tt::up($path),
            ];

            \tt::success($data);
        }else{
            \tt::error($file->getError());
        }
    }

    function imgDataUrl() {
        $rootPath = config('app.upload.root_path');
        $savePath = $this->tempDir.date('ym/d-His-').substr(microtime(),2,8).'.png';
        $fullPath = $rootPath.$savePath;
        $dirPath = dirname($fullPath);

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $dataUrl = $_POST['imgDataUrl'];
        $dataUrl = substr($dataUrl, strpos($dataUrl, ",") + 1);
        $dataUrl = base64_decode($dataUrl) ?: \tt::error('图片数据错误');

        file_put_contents($fullPath, $dataUrl) or \tt::error('图片保存失败');

        $data = [
            'path' => $savePath,
            'url' => \tt::up($savePath),
        ];

        \tt::success($data);
    }

    static function moveDir($tempPath, $newDir) {
        if (!$tempPath) return '';

        $self = new static();

        if (!str::startsWith($tempPath, $self->tempDir)) {
            return $tempPath;
        }

        if (!str::endsWith($newDir, '/')) {
            $newDir .= '/';
        }

        $newPath = str_replace($self->tempDir, $newDir, $tempPath);

        $rootPath = config('app.upload.root_path');
        $tempFull = $rootPath.$tempPath;
        $newFull = $rootPath.$newPath;

        $dirPath = dirname($newFull);
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        rename($tempFull, $newFull);

        return $newPath;
    }
}