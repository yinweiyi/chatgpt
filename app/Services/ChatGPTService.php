<?php

namespace App\Services;

use App\Vendors\Http;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ChatGPTService
{
    private array $config;

    const DOMAIN = 'https://api.openai.com';

    /**
     * ChatGPTService construct
     */
    public function __construct()
    {
        $this->config = Config::get('chatgpt');
    }

    /**
     * 处理消息
     *
     * @param $post
     * @return array
     */
    public function handleMessage($post): array
    {
        sleep(2);

        return ['type' => 'chat', 'data' => [
            "role" => "assistant",
            "content" => "\n\nHello there, how may I assist you today?",
        ]];

        $type = $post['type'] ?? head($this->config['types']);
        if (!in_array($type, $this->config['types'])) {
            Log::channel('http_client')->error('Request type' . $type . ' is not allow');
            return [];
        }
        $data = [];
        if ($type === 'chat') {
            $messages = $post['messages'];
            if (empty($messages)) {
                return [];
            }
            $data = $this->chat($messages);
        }

        return compact('type', 'data');
    }

    /**
     * 聊天
     *
     * @param array $messages
     * @return array
     */
    public function chat(array $messages): array
    {
        $params = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages
        ];
        $method = '/v1/chat/completions';

        $result = $this->request($params, $method, $this->getHeader());
        return $result['choices'][0]['message'] ?? [];
    }

    /**
     * 发送请求
     *
     * @param $params
     * @param $method
     * @param $header
     * @return array
     */
    public function request($params, $method, $header): array
    {
        try {
            $response = Http::json(self::DOMAIN . $method, $params, 'post', $header);
            if ($response->getStatusCode() == Response::HTTP_OK) {
                $contents = $response->getBody()->getContents();
                Log::channel('http_client')->info('chatgpt response:' . $contents);
                return \json_decode($contents, true);

            }
            return [];
        } catch (GuzzleException $exception) {
            Log::error('Get chatgpt response error:' . $exception->getMessage());
            return [];
        }
    }


    /**
     * 获取头部
     *
     * @return string[]
     */
    public function getHeader(): array
    {
        return ['Authorization' => 'Bearer ' . $this->config['api_key']];
    }
}
