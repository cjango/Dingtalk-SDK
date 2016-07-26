<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools\DDing;

use tools\DDing;

/**
 * 基础工具类
 */
class Utils extends DDing
{

    /**
     * 钉钉接口调用
     */
    public function api($apiUrl, $params = null, $method = 'GET')
    {
        $url    = parent::BASE_URL . $apiUrl;
        $method = strtoupper($method);
        if ($method == 'GET' && !empty($params) && is_array($params)) {
            $url .= '?' . http_build_query($params);
        }
        $result = self::https($url, $method, $params, ['Content-Type' => 'application/json']);
        if ($result !== false) {
            return self::$result = json_decode($result, true);
        } else {
            return false;
        }
    }

    /**
     * curl操作函数
     * @param  string $url        请求地址
     * @param  string $method     提交方式
     * @param  array  $postFields 提交内容
     * @param  array  $header     请求头
     * @return mixed              返回数据
     */
    public static function http($url, $method = "GET", $postFields = null, $headers = null)
    {
        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'])) {
            return false;
        }

        $opts = [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_URL            => $url,
            CURLOPT_FAILONERROR    => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
        ];

        if ($method == 'POST' && !is_null($postFields)) {
            $opts[CURLOPT_POSTFIELDS] = $postFields;
        }

        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = false;
        }

        if (!empty($headers) && is_array($headers)) {
            $httpHeaders = [];
            foreach ($headers as $key => $value) {
                array_push($httpHeaders, $key . ":" . $value);
            }
            $opts[CURLOPT_HTTPHEADER] = $httpHeaders;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $err  = curl_errno($ch);
        curl_close($ch);
        if ($err > 0) {
            return curl_error($ch);
        } else {
            return $data;
        }
    }
}
