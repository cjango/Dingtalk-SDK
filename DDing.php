<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

/**
 *
 */
class DDing
{

    /**
     * 钉钉实例
     * @var object
     */
    protected static $instance;

    /**
     * 接口调用地址
     * @var string
     */
    protected static $apiUrl = 'https://oapi.dingtalk.com/';

    /**
     * 请求头信息
     * @var array
     */
    protected static $headers = [
        'Content-Type' => 'application/json',
    ];

    /**
     * 钉钉的配置信息
     * @var array
     */
    protected static $config = [
        'agentid'    => '',
        'corpid'     => '',
        'corpsecret' => '',
        'ssosecret'  => '',
    ];

    /**
     * 实例化钉钉SDK
     * @param array $config 配置参数
     */
    public function __construct($config = [])
    {
        if (!empty($config) && is_array($config)) {
            self::$config = array_merge(self::$config, $config);
        }
    }

    /**
     * 实例化的静态方法
     * @param  array   $config 配置信息
     * @param  boolean $force  强制重新实例化
     * @return \tools\DDing
     */
    public static function instance($config = [], $force = false)
    {
        if (is_null(self::$instance) || $force == true) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    /**
     * 设置/获取 配置变量
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public static function config($key, $value = null)
    {
        if (is_null($value)) {
            return self::$config[$key];
        } else {
            self::$config[$key] = $value;
        }
    }

    /**
     * JS-API权限验证参数生成
     * @return array
     */
    public static function ddConfig()
    {
        $randomStr = uniqid();
        $timestamp = time();
        $config    = [
            'agentId'   => self::$config['agentid'],
            'corpId'    => self::$config['corpid'],
            'timeStamp' => $timestamp,
            'nonceStr'  => $randomStr,
        ];
        $config['signature'] = self::sign($randomStr, $timestamp);
        return $config;
    }

    /**
     * 钉钉签名算法
     * @param  string $jsapi_ticket
     * @param  string $noncestr
     * @param  string $timestamp
     * @param  string $url
     * @return string
     */
    private static function sign($noncestr, $timestamp)
    {
        $signArr = [
            'jsapi_ticket' => self::$config['jsapi_ticket'],
            'noncestr'     => $noncestr,
            'timestamp'    => $timestamp,
            'url'          => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], // 获取当前页面地址 有待优化
        ];
        ksort($signArr);
        $signStr = urldecode(http_build_query($signArr));
        return sha1($signStr);
    }
}
