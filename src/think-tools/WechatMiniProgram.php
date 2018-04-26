<?php
/**
 * User: cos800
 * Date: 2018/4/24
 * Time: 下午5:31
 */

namespace tt;


use Curl\Curl;

class WechatMiniProgram
{
    public $appid = '';
    public $secret = '';

    function __construct($appid, $secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
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
        $curl = new Curl();
        $curl->get("https://api.weixin.qq.com/sns/jscode2session", [
            'appid' => $this->appid,
            'secret' => $this->secret,
            'js_code' => $jsCode,
            'grant_type' => 'authorization_code',
        ]);
        
        if ($curl->error) {
            throw new \Exception($curl->errorMessage);
        }

        $data = json_decode($curl->response);
        if (!$data) throw new \Exception('json decode fail');

        if ($data->errcode) throw new \Exception($data->errcode.': '.$data->errmsg);

        return $data;
    }
}