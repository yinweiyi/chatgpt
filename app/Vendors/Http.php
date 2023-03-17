<?php


namespace App\Vendors;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class Http
{
    /**
     * @var ?Client
     */
    protected static ?Client $client = null;


    /**
     * curl 提交json
     * @param $url
     * @param array $params
     * @param string $method
     * @param array $header
     * @param int $timeout
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public static function json($url, array $params = [], string $method = 'post', array $header = [], int $timeout = 30): ResponseInterface
    {
        $client = self::getClient();
        $response =  $client->request($method, $url, ['json' => $params, 'connect_timeout' => $timeout, 'headers' => $header]);
        self::log($url, 'json', $header, $params);
        return $response;
    }

    /**
     * get请求
     *
     * @param $url
     * @param array $params
     * @param array $header
     * @param int $timeout
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public static function get($url, array $params = [], array $header = [], int $timeout = 10): mixed
    {
        $client = self::getClient();
        $response = $client->get($url, ['query' => $params, 'connect_timeout' => $timeout, 'headers' => $header]);
        self::log($url, 'get', $header, $params);
        return $response;
    }


    /**
     * 获取client
     *
     * @param array $config
     * @return Client|null
     */
    public static function getClient(array $config = []): ?Client
    {
        if ($client = self::$client) {
            return $client;
        }
        self::$client = new Client($config);

        return self::$client;
    }

    /**
     * 第三方请求日志
     *
     * @param $url
     * @param $method
     * @param $header
     * @param $params
     */
    private static function log($url, $method, $header, $params): void
    {
        $clientIp = request()->ip();
        Log::channel('http_client')->info(\json_encode(compact('clientIp','url', 'method', 'header', 'params'), JSON_UNESCAPED_UNICODE));
    }
}

