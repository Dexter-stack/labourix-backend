<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use OpenAI\Client;

class OpenAIProvider implements AIProviderInterface
{
    public function __construct(private Client $client) {}

    public function chat(array $messages, array $options = []): string
    {
        $response = $this->client->chat()->create(array_merge([
            'model'       => config('labourix.ai.openai.model', 'gpt-4o-mini'),
            'temperature' => 0.2,
            'messages'    => $messages,
        ], $options));

        return $response->choices[0]->message->content ?? '';
    }
}
