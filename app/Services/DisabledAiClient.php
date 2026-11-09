<?php

namespace App\Services;

use App\Exceptions\AiServiceException;

final class DisabledAiClient implements AiClientInterface
{
    public function isConfigured(): bool
    {
        return false;
    }

    public function supportsImageEditing(): bool
    {
        return false;
    }

    public function chatJson(array $messages, float $temperature = 0.2, int $maxTokens = 4000): array
    {
        throw AiServiceException::notConfigured();
    }

    public function imageEdit(array $absoluteImagePaths, string $prompt): string
    {
        throw AiServiceException::notConfigured();
    }
}
