<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(protected AiChatbotService $aiChatbotService) {}

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string',
            'language' => 'nullable|in:en,am',
        ]);

        $userId = $request->user()?->id;
        $result = $this->aiChatbotService->respond(
            message: $validated['message'],
            userId: $userId,
            sessionId: $validated['session_id'] ?? null,
            lang: $validated['language'] ?? 'en'
        );

        return response()->json($result);
    }
}
