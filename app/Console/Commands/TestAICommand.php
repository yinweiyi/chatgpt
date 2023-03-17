<?php

namespace App\Console\Commands;

use App\Services\ChatGPTService;
use Illuminate\Console\Command;

class TestAICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private ChatGPTService $chatGPTService;

    /**
     * @param ChatGPTService $chatGPTService
     */
    public function __construct(ChatGPTService $chatGPTService)
    {
        parent::__construct();
        $this->chatGPTService = $chatGPTService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $messages = [
            ["role" => "user", "content" => "Hello!"],
            ["role" => "assistant", "content" => "\n\nHello! How may I assist you today?"],
            ["role" => "user", "content" => "请用中文翻译一下你刚说的话"],
        ];
        $result = $this->chatGPTService->chat($messages);
        $this->info('Result:' . \json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}
