<?php

namespace App\Services;

interface AiClientInterface
{
    public function isConfigured(): bool;

    public function supportsImageEditing(): bool;

    /**
     * @param  list<array{role: string, content: mixed}>  $messages
     * @return array<string, mixed>
     */
    public function chatJson(array $messages, float $temperature = 0.2, int $maxTokens = 4000): array;

    /**
     * @param  list<string>  $absoluteImagePaths
     */
    public function imageEdit(array $absoluteImagePaths, string $prompt): string;
}
