<?php

namespace App\AI\Contracts;

interface AIProviderInterface
{
    /**
     * Send a chat completion request.
     *
     * @param  array  $messages  Array of ['role' => string, 'content' => string]
     * @param  array  $options   Provider-specific overrides (model, temperature, etc.)
     */
    public function chat(array $messages, array $options = []): string;
}
