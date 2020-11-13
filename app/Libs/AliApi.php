<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2020/2/17
 */

namespace App\Libs;


use App\Entities\Log;

class AliApi
{
    private $host = '';
    private $path = '';
    private $apiTitle = '阿里云接口';
    //阿里云appcode
    private $appCode = '';

    public function __construct()
    {

    }

    /**
     * 记录日志 add by gui
     * @param string $type
     * @param $content
     */
    protected function logs($type = 'info', $content = null)
    {
        switch ($type) {
            case 'info':
                $log_type = Log::LOG_TYPE;
                break;
            case 'error':
                $log_type = Log::DEBUG_TYPE;
                break;
            default:
                $log_type = Log::LOG_TYPE;
        }
        if (!is_string($content)) {
            $content = json_encode($content);
        }
        $content = $this->apiTitle . '：' . $content;
        Log::createLog($log_type, $content);
    }

    /**
     * GET请求接口 add by gui
     * @param array $params
     * @return bool|string
     */
    public function apiGetData($params = [])
    {
        $url     = $this->getApiUrl();
        $method  = 'GET';
        $headers = array();
        $query   = http_build_query($params);
        if ($query) {
            $url .= '?' . $query;
        }
        array_push($headers, "Authorization:APPCODE " . $this->appCode);
        $this->logs('info', 'GET请求接口，' . $url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        //curl_setopt($curl, CURLOPT_HEADER, true);   如不输出json, 请打开这行代码，打印调试头部状态码。

        if (1 == strpos("$" . $this->host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $out_put = curl_exec($curl);
        //状态码:
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->checkHttpCode($httpCode);
        if (curl_errno($curl)) {
            $this->logs('error', 'GET请求接口返回失败，' . 'Curl error: ' . curl_error($curl));
        }

        return $out_put;
    }

    /**
     * 检查Http状态是否成功 add by gui
     * @param $httpCode
     * @return bool
     */
    private function checkHttpCode($httpCode)
    {
        //200 正常；400 URL无效；401 appCode错误； 403 次数用完； 500 API网管错误
        $check = false;
        switch ($httpCode) {
            case 200:
                $check = true;
                break;
            case 400:
                $msg = 'URL无效';
                break;
            case 401:
                $msg = 'appCode错误';
                break;
            case 403:
                $msg = '次数用完';
                break;
            case 500:
                $msg = 'API网管错误';
                break;
        }
        if ($check !== true) {
            $this->logs('error', '接口返回状态码异常，状态码：' . $httpCode . '；' . $msg);
        }
        return $check;
    }

    /**
     * @param string $path
     * @return AliApi
     */
    public function setPath(string $path): AliApi
    {
        $this->path = $path;
        return $this;
    }

    protected function getApiUrl()
    {
        return $this->host . $this->path;
    }

    /**
     * @param string $host
     * @return AliApi
     */
    public function setHost(string $host): AliApi
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param string $appCode
     * @return AliApi
     */
    public function setAppCode(): AliApi
    {
        $appCode = '';
        if($appCode == ''){
            $appCode = get_config_value('ALI_WULIU_APP_CODE','');
        }
        if($appCode == ''){
            $appCode = config('qinglong.aliyun_wuliu_app_code');
        }
        $this->appCode = $appCode;
        return $this;
    }

    /**
     * @param string $apiTitle
     * @return AliApi
     */
    public function setApiTitle(string $apiTitle): AliApi
    {
        $this->apiTitle = $apiTitle;
        return $this;
    }
}
