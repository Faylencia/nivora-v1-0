<?php

namespace App\Services;

use App\Exceptions\AiServiceException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Thin HTTP wrapper around the OpenAI API. Never exposed to the frontend;
 * the API key is only read from configuration (OPENAI_API_KEY).
 */
final class OpenAiClient implements AiClientInterface
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $baseUrl,
        private readonly string $visionModel,
        private readonly string $imageModel,
        private readonly string $imageQuality,
        private readonly string $imageSize,
        private readonly int $timeout,
    ) {}

    public static function fromConfig(): self
    {
        /** @var array{key: ?string, base_url: string, vision_model: string, image_model: string, image_quality: string, image_size: string, timeout: int} $config */
        $config = config('services.openai');

        return new self(
            $config['key'] ?? null,
            rtrim((string) ($config['base_url'] ?? 'https://api.openai.com/v1'), '/'),
            (string) ($config['vision_model'] ?? 'gpt-4o'),
            (string) ($config['image_model'] ?? 'gpt-image-1.5'),
            (string) ($config['image_quality'] ?? 'medium'),
            (string) ($config['image_size'] ?? 'auto'),
            (int) ($config['timeout'] ?? 180),
        );
    }

    public function isConfigured(): bool
    {
        return is_string($this->apiKey) && trim($this->apiKey) !== '';
    }

    public function supportsImageEditing(): bool
    {
        return true;
    }

    /**
     * Run a chat completion that must return a JSON object.
     *
     * @param  list<array{role: string, content: mixed}>  $messages
     * @return array<string, mixed>
     */
    public function chatJson(array $messages, float $temperature = 0.2, int $maxTokens = 4000): array
    {
        $this->ensureConfigured();

        $response = Http::withToken((string) $this->apiKey)
            ->acceptJson()
            ->timeout($this->timeout)
            ->post($this->baseUrl.'/chat/completions', [
                'model' => $this->visionModel,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
                'response_format' => ['type' => 'json_object'],
            ]);

        $this->ensureSuccessful($response, 'analisis foto');

        $content = $response->json('choices.0.message.content');
        $decoded = is_string($content) ? json_decode($content, true) : null;

        if (! is_array($decoded)) {
            throw new AiServiceException('Respons AI tidak dapat dibaca sebagai JSON. Coba lagi.');
        }

        return $decoded;
    }

    /**
     * Edit/generate an image using the original store photos as reference.
     *
     * @param  list<string>  $absoluteImagePaths
     * @return string Binary image data (PNG).
     */
    public function imageEdit(array $absoluteImagePaths, string $prompt): string
    {
        $this->ensureConfigured();

        if ($absoluteImagePaths === []) {
            throw new AiServiceException('Foto referensi tidak tersedia untuk membuat visualisasi.');
        }

        $multipart = [
            ['name' => 'model', 'contents' => $this->imageModel],
            ['name' => 'prompt', 'contents' => $prompt],
            ['name' => 'size', 'contents' => $this->imageSize],
            ['name' => 'quality', 'contents' => $this->imageQuality],
        ];

        foreach ($absoluteImagePaths as $path) {
            $stream = fopen($path, 'rb');

            if ($stream === false) {
                throw new AiServiceException('Foto toko tidak dapat dibaca dari penyimpanan.');
            }

            $mime = mime_content_type($path) ?: 'image/jpeg';

            $multipart[] = [
                'name' => 'image[]',
                'contents' => $stream,
                'filename' => basename($path),
                'headers' => ['Content-Type' => $mime],
            ];
        }

        $response = Http::withToken((string) $this->apiKey)
            ->acceptJson()
            ->asMultipart()
            ->timeout($this->timeout)
            ->post($this->baseUrl.'/images/edits', $multipart);

        $this->ensureSuccessful($response, 'pembuatan visualisasi');

        $base64 = $response->json('data.0.b64_json');
        $binary = is_string($base64) ? base64_decode($base64, true) : false;

        if (! is_string($binary) || $binary === '') {
            throw new AiServiceException('Layanan AI tidak mengembalikan gambar. Coba lagi.');
        }

        return $binary;
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw AiServiceException::notConfigured();
        }
    }

    private function ensureSuccessful(Response $response, string $operation): void
    {
        if ($response->successful()) {
            return;
        }

        $payload = $response->json();

        throw AiServiceException::fromResponse(
            $operation,
            $response->status(),
            is_array($payload) ? $payload : null,
        );
    }
}
