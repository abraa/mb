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


class WeChat
{
    public static $token = '';
    public static $app_id = WECHAT_APPID;
    public static $app_secret = WECHAT_APPSECRET;
    private static $access_token = '';
    private static $open_id;
    private static $weChat_id;
    private static $weChatApiUrl = 'https://api.weixin.qq.com';
    private static $weChatOpenUrl = 'https://open.weixin.qq.com';


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
            $ret = static::getData('cgi-bin/token', $param, 'get');
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
        $ret = self::getData('cgi-bin/user/info', $param, 'GET');
        $data = json_decode($ret, true);
        if ($data['errcode'] == 0) {
            return $data;
        }
        return array();
    }

    /**
     * 获取openId
     * @param string $redirect_uri
     * @param string $state
     * @return null
     */
    public static function getOpenId($redirect_uri = '', $state = '')
    {
        $openid = isset($_SESSION['sopenid']) ? $_SESSION['sopenid'] : '';
        if (!empty($openid)) {
            return $openid;
        }
        $redirect_uri = self::getRedirectUri($redirect_uri);
        $code = empty($_REQUEST['code']) ? null : trim($_REQUEST['code']);
        if (empty($code)) {
            self::getCode('snsapi_base', $redirect_uri, $state);
        } else {
            $param = array(
                'appid' => self::$app_id,
                'secret' => self::$app_secret,
                'code' => $code,
                'grant_type' => 'authorization_code'
            );
            $ret = self::getData('sns/oauth2/access_token', $param, 'get');
            $data = json_decode($ret, true);
            $openid = isset($data['openid']) && !empty($data['openid']) ? $data['openid'] : null;
            $_SESSION['sopenid'] = $openid;
            return $openid;
        }
    }


    /**
     * 设置微信授权回调地址
     * @param string $redirect_uri
     * @return string
     */
    private function getRedirectUri($redirect_uri = '')
    {
        $redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if(!empty($redirect_uri)){
            $redirectUri = $redirect_uri;
        }
        return $redirectUri;
    }


    /**
     * 获取code
     * @param $scope
     * @param $redirect_uri
     * @param $state
     */
    private function getCode($scope, $redirect_uri, $state)
    {
        $state = (!empty($state) && strlen($state) <= 128 && preg_match('/^[A-Za-z0-9]+$/', $state)) ? $state : 'state';
        $param = array(
            'appid' => self::$app_id,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state . '#wechat_redirect'
        );
        $buff = '';
        ksort($param);
        foreach ($param as $k => $v) {
            $buff .= $k . '=' . $v . '&';
        }
        if (strlen($buff) > 0) {
            $buff = substr($buff, 0, strlen($buff) - 1);
        }
        $url = self::$weChatOpenUrl . '/connect/oauth2/authorize?' . $buff;
        header("Location: $url");  //跳转过去，为了获取code
    }


    /**
     * 获取接口数据
     * @param string $instruct 指令
     * @param array $data 数据
     * @param string $method 类型
     * @param int $debug 打印错误
     * @return bool|Base\错误返回
     */
    private function getData($instruct, $data = array(), $method = 'POST')
    {
        $url = self::$weChatApiUrl . "/" . $instruct;
        $ret = Curl::request($url, $data, '', $method);
        if ($ret) {
            return $ret;
        } else {
            return false;
        }
    }
}