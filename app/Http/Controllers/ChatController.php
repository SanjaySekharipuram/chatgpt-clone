<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('OPENAI_API_URL') . '/chat/completions', [
                'model' => 'gpt-3.5-turbo', 
                'messages' => [
                    ['role' => 'system', 'content' => 'You are ChatGPT, a large language model trained by OpenAI.'],
                    ['role' => 'user', 'content' => $request->input('message')],
                ],
            ]);

            if ($response->failed()) {
                // Handle the failed request
                $statusCode = $response->status();
                $errorMessage = $response->body();
                // Log or display the error message as needed
                return response()->json(['error' => $errorMessage], $statusCode);
            }

            $responseData = $response->json();
            $reply = $responseData['choices'][0]['message']['content'];

            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['error' => "Something went wrong"], 422);
        }
    }
}
