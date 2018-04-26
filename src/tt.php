<?php
/**
 * User: cos800
 * Date: 2018/4/9
 * Time: 下午5:57
 */

class tt
{
    static function ff() {
        echo 'ffff';
    }


    static function up($path, $width=FALSE, $height=FALSE, $type=3) {
        // 外链直接输出
        if (!$path or !strncasecmp('http://', $path, 7)) {
            return $path;
        }
        // 多图 处理第一张图片
        if ($tmp = strpos($path, ',')) {
            $path = substr($path, 0, $tmp);
        }

        if ($path{0}==='/') {
            $uploadPath = '.';
            $uploadUrl = '';
        }else{
            $uploadPath =  config('app.upload.root_path');
            $uploadUrl = config('app.upload.base_url');
        }
        if ($width and $height) {
            // 缩略图路径
            $newPath = substr_replace($path, "_{$width}x{$height}", strrpos($path,'.'), 0);
            $newRealPath = $uploadPath.$newPath; // 缩略图完整路径
            $realPath = $uploadPath.$path; // 原图完成路径

            // 不存在则生成缩略图
            if (!is_file($newRealPath) and is_file($realPath)) {
//            $Img = new Think\Image();
//            $Img->open($realPath);
                $Img = \think\Image::open($realPath);
                try{
                    $Img->thumb($width, $height, $type);
                    $Img->save($newRealPath);
                } catch (Exception $e) {
//                echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }
            return $uploadUrl.$newPath;
        }
        return $uploadUrl.$path; // 返回原图URL
    }

    static function wxmp($appid='', $secret='') {
        if (empty($appid) and empty($secret)) {
            $appid = config('app.wxmp.appid');
            $secret = config('app.wxmp.secret');
        }
        $wxmp = new \tt\WechatMiniProgram($appid, $secret);
        return $wxmp;
    }
}