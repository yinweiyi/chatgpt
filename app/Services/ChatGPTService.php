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
        $data = null;
        $type = $post['type'] ?? head($this->config['types']);
        if (!in_array($type, $this->config['types'])) {
            Log::channel('http_client')->error('Request type' . $type . ' is not allow');
            return compact('type', 'data');
        }

        if ($type === 'chat') {
            $messages = $post['messages'];
            if (empty($messages)) {
                return compact('type', 'data');
            }
            $data = $this->chat($messages);
        } elseif ($type === 'image_create') {
            $params = $post['params'] ;
            if (empty($params)) {
                return compact('type', 'data');
            }
            $data = $this->imagesGenerations($params);
        }

        return compact('type', 'data');
    }

    /**
     * 聊天
     *
     * @param array $messages
     * @return array|null
     */
    public function chat(array $messages): array|null
    {
        $params = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages
        ];
        $method = '/v1/chat/completions';

        $result = $this->request($params, $method, $this->getHeader());
        return $result['choices'][0]['message'] ?? null;
    }

    /**
     * @param $params
     * prompt:  A text description of the desired image(s). The maximum length is 1000 characters.
     * n: The number of images to generate. Must be between 1 and 10.
     * size: The size of the generated images. Must be one of 256x256, 512x512, or 1024x1024.
     * @return void
     */
    public function imagesGenerations($params)
    {
        $method = '/v1/images/generations';
        $result = $this->request($params, $method, $this->getHeader());
        return $result['data'] ?? null;
    }

    /**
     * 发送请求
     *
     * @param $params
     * @param $method
     * @param $header
     * @return array|null
     */
    public function request($params, $method, $header): array|null
    {
        try {
            $response = Http::json(self::DOMAIN . $method, $params, 'post', $header);
            if ($response->getStatusCode() == Response::HTTP_OK) {
                $contents = $response->getBody()->getContents();
                Log::channel('http_client')->info('chatgpt response:' . $contents);
                return \json_decode($contents, true);
            }
            return null;
        } catch (GuzzleException $exception) {
            Log::error('Get chatgpt response error:' . $exception->getMessage());
            return null;
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
