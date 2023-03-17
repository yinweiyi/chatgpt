<?php

namespace App\Http\Controllers\Api;

use App\Services\ChatGPTService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatgptController
{

    /**
     * @param Request $request
     * @param ChatGPTService $chatGPTService
     * @return JsonResponse
     */
    public function chat(Request $request, ChatGPTService $chatGPTService): JsonResponse
    {
        $response = $chatGPTService->handleMessage($request->post());
        return response()->json($response);
    }
}
