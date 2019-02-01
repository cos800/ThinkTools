<?php
/**
 * User: cos800
 * Date: 2018/4/24
 * Time: 下午5:31
 */

namespace tt;


use Curl\Curl;
use think\facade\Cache;

class Weixin
{
    public $appid = '';
    public $secret = '';

    function __construct($appid, $secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    function accessToken() {
        $cacheId = 'access_token_'.$this->appid;

        $token = Cache::get($cacheId);

        if (!$token) {
            $data = static::httpGet("https://api.weixin.qq.com/cgi-bin/token", [
                'grant_type' => 'client_credential',
                'appid' => $this->appid,
                'secret' => $this->secret,
            ]);

            $token = $data->access_token;

            Cache::set($cacheId, $token, $data->expires_in);
        }

        return $token;
    }

    function userInfo($openid, $lang='zh_CN') {
        $token = $this->accessToken();
        $data = static::httpGet('https://api.weixin.qq.com/cgi-bin/user/info', [
            'access_token' => $token,
            'openid' => $openid,
            'lang' => $lang,
        ]);

        return $data;
    }

    static function decryptData($sessionKey, $encryptedData, $iv) {
        $aesKey = base64_decode($sessionKey);
        if (!$aesKey) throw new \Exception('sessionKey error');

        $aesIV = base64_decode($iv);
        if (!$aesIV) throw new \Exception('iv error');

        $aesCipher = base64_decode($encryptedData);
        if (!$aesCipher) throw new \Exception('encryptedData error');

        $result = openssl_decrypt($aesCipher,"AES-128-CBC", $aesKey,1, $aesIV);
        if (!$result) throw new \Exception('openssl decrypt fail');

        $data = json_decode($result);
        if (!$data) throw new \Exception('json decode fail');

        return $data;
    }

    function jscode2session($jsCode) {
        $data = static::httpGet("https://api.weixin.qq.com/sns/jscode2session", [
            'appid' => $this->appid,
            'secret' => $this->secret,
            'js_code' => $jsCode,
            'grant_type' => 'authorization_code',
        ]);

        return $data;
    }

    static function httpGet($url, $query=[]) {
        $curl = new Curl();
        $curl->get($url, $query);

        if ($curl->error) {
            throw new \Exception($curl->errorMessage);
        }

        if (is_string($curl->response)) {
            $data = json_decode($curl->response);
            if (!$data) throw new \Exception('json decode fail');
        }else{
            $data = $curl->response;
        }


        if ($data->errcode) throw new \Exception($data->errcode.': '.$data->errmsg);

        return $data;
    }
}