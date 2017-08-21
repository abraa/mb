<?php
/**
 * 微信公众平台 消息接口
 * 流程：1、当用户回复公众平台时，消息传到本地址。2、本地址程序可回复消息给用户。
 * 微信服务器在五秒内收不到响应会断掉连接。
 * http://mp.weixin.qq.com/wiki/
 *
 * @date   2013-05-06
 */
namespace Common\Extend;

use Common\Extend\Base\Curl;

class WeChat
{
    public static $token = '';
    public static $app_id = WECHAT_APPID;
    public static $app_secret = WECHAT_APPSECRET;
    private static $access_token = '';
    private static $open_id;
    private static $weChat_id;
    private static $weChatApiUrl = 'https://api.weixin.qq.com';


    /**
     * 获取access_token
     * @return string
     */
    public static function getAccessToken()
    {
        if (empty(self::$access_token)) {
            $param = array(
                'grant_type' => 'client_credential',
                'appid' => self::$app_id,
                'secret' => self::$app_secret,
            );
            $ret = static::getData('cgi-bin/token', $param);
            if (!empty($ret)) {
                $data = json_decode($ret, true);
                self::$access_token = $data['access_token'];
            }
        }
        return self::$access_token;
    }

    /**
     * 获取个人信息
     * @param string $openId 用户的openId
     * @return array|mixed
     */
    public static function getWeChatInfo($openId = '')
    {
        $openId = empty($openId) ? self::$open_id : $openId;
        $param = array(
            'access_token' => self::getAccessToken(),
            'openid' => $openId,
            'lang' => 'zh_CN',
        );
        $ret = self::getData('/user/info', $param);
        $data = json_decode($ret, true);
        if (isset($data['errcode']) && $data['errcode'] == 0) {
            return $data;
        }
        return array();
    }


    /**
     * 获取接口数据
     * @param string $instruct 指令
     * @param array $data 数据
     * @param string $method 类型
     * @param int $debug 打印错误
     * @return bool|Base\错误返回
     */
    private function getData($instruct, $data = array(), $method = 'POST', $debug = 0)
    {
        $curl = new Curl();
        $url = self::$weChatApiUrl . "/" . $instruct;
        if (strtolower($method) == 'post') {
            $ret = $curl->post($url, $data);
        } else {
            $ret = $curl->get($url, $data);
        }
        if ($ret) {
            return $ret;
        } else {
            if ($debug) {
                echo '错误码：' . $curl->errno() . ',错误信息' . $curl->error();
                exit;
            }
            return false;
        }
    }
}