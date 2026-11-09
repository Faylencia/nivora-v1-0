<?php

namespace App\Services;

use App\Exceptions\AiServiceException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Native Gemini Developer API client for the free vision/analysis tier.
 * It intentionally does not generate images: image models are paid-only.
 */
final class GeminiClient implements AiClientInterface
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $baseUrl,
        private readonly string $visionModel,
        private readonly int $timeout,
    ) {}

    public static function fromConfig(): self
    {
        /** @var array{key: ?string, base_url: string, vision_model: string, timeout: int} $config */
        $config = config('services.gemini');

        return new self(
            $config['key'] ?? null,
            rtrim((string) ($config['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta'), '/'),
            (string) ($config['vision_model'] ?? 'gemini-3.6-flash'),
            (int) ($config['timeout'] ?? 180),
        );
    }

    public function isConfigured(): bool
    {
        return is_string($this->apiKey) && trim($this->apiKey) !== '';
    }

    public function supportsImageEditing(): bool
    {
        return false;
    }

    /**
     * @param  list<array{role: string, content: mixed}>  $messages
     * @return array<string, mixed>
     */
    public function chatJson(array $messages, float $temperature = 0.2, int $maxTokens = 4000): array
    {
        $this->ensureConfigured();

        ['systemInstruction' => $systemInstruction, 'contents' => $contents] = $this->convertMessages($messages);

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'maxOutputTokens' => $maxTokens,
                'thinkingConfig' => ['thinkingLevel' => 'LOW'],
            ],
        ];

        if ($systemInstruction !== null) {
            $payload['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];
        }

        $response = Http::withHeaders([
            'x-goog-api-key' => (string) $this->apiKey,
            'Accept' => 'application/json',
        ])->acceptJson()
            ->timeout($this->timeout)
            ->post($this->baseUrl.'/models/'.$this->visionModel.':generateContent', $payload);

        $this->ensureSuccessful($response);

        $candidate = $response->json('candidates.0');
        $finishReason = data_get($candidate, 'finishReason');
        $parts = data_get($candidate, 'content.parts', []);
        $parts = is_array($parts) ? $parts : [];

        if ($finishReason === 'MAX_TOKENS') {
            Log::warning('Gemini response truncated', [
                'model' => $this->visionModel,
                'finishReason' => $finishReason,
                'part_types' => collect($parts)->map(fn (mixed $part): mixed => is_array($part) ? array_keys($part) : gettype($part))->all(),
            ]);

            throw new AiServiceException('Respons AI terpotong sebelum JSON selesai. Coba lagi.');
        }

        if (in_array($finishReason, ['SAFETY', 'PROHIBITED_CONTENT', 'BLOCKLIST'], true)) {
            throw new AiServiceException('Respons AI diblokir oleh filter keamanan. Gunakan foto yang jelas dan tidak mengandung data sensitif.');
        }

        $text = collect($parts)
            ->filter(fn (mixed $part): bool => is_array($part) && (bool) ($part['thought'] ?? false) === false && is_string($part['text'] ?? null))
            ->pluck('text')
            ->implode('');

        $jsonText = $this->extractJsonObject(trim($text));
        $decoded = $jsonText !== null ? json_decode($jsonText, true) : null;

        if (! is_array($decoded)) {
            Log::warning('Gemini response could not be parsed', [
                'model' => $this->visionModel,
                'finishReason' => $finishReason,
                'part_types' => collect($parts)->map(fn (mixed $part): mixed => is_array($part) ? array_keys($part) : gettype($part))->all(),
                'text_length' => strlen($text),
            ]);

            throw new AiServiceException('Respons Gemini tidak dapat dibaca sebagai JSON. Coba lagi.');
        }

        return $decoded;
    }

    public function imageEdit(array $absoluteImagePaths, string $prompt): string
    {
        throw new AiServiceException('Generate gambar tidak tersedia pada mode Gemini gratis. Gunakan denah 2D, atau aktifkan provider berbayar.');
    }

    /**
     * Extract a JSON object even if the model wraps it in markdown or prose.
     */
    private function extractJsonObject(string $text): ?string
    {
        $candidate = trim($text);

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $candidate, $matches) === 1) {
            $candidate = trim($matches[1]);
        }

        $start = strpos($candidate, '{');
        $end = strrpos($candidate, '}');

        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        return substr($candidate, $start, $end - $start + 1);
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw AiServiceException::notConfigured();
        }
    }

    private function ensureSuccessful(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $payload = $response->json();

        throw AiServiceException::fromResponse(
            'analisis foto',
            $response->status(),
            is_array($payload) ? $payload : null,
        );
    }

    /**
     * @param  list<array{role: string, content: mixed}>  $messages
     * @return array{systemInstruction: ?string, contents: list<array{role: string, parts: list<array<string, mixed>>}>}
     */
    private function convertMessages(array $messages): array
    {
        $systemParts = [];
        $contents = [];

        foreach ($messages as $message) {
            $role = (string) ($message['role'] ?? 'user');
            $content = $message['content'] ?? '';

            if ($role === 'system') {
                $systemParts[] = $this->contentText($content);

                continue;
            }

            $parts = $this->contentParts($content);

            if ($parts !== []) {
                $contents[] = [
                    'role' => $role === 'assistant' ? 'model' : 'user',
                    'parts' => $parts,
                ];
            }
        }

        return [
            'systemInstruction' => trim(implode("\n\n", array_filter($systemParts))) ?: null,
            'contents' => $contents,
        ];
    }

    private function contentText(mixed $content): string
    {
        if (is_string($content)) {
            return $content;
        }

        if (! is_array($content)) {
            return '';
        }

        return collect($content)
            ->filter(fn (mixed $part): bool => is_array($part) && ($part['type'] ?? null) === 'text')
            ->pluck('text')
            ->filter(fn (mixed $text): bool => is_string($text))
            ->implode("\n");
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function contentParts(mixed $content): array
    {
        if (is_string($content)) {
            return trim($content) === '' ? [] : [['text' => $content]];
        }

        if (! is_array($content)) {
            return [];
        }

        $parts = [];

        foreach ($content as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (($item['type'] ?? null) === 'text' && is_string($item['text'] ?? null)) {
                $parts[] = ['text' => $item['text']];

                continue;
            }

            if (($item['type'] ?? null) === 'image_url') {
                $url = (string) data_get($item, 'image_url.url', '');

                if (preg_match('/^data:(image\/(?:png|jpeg|webp));base64,(.+)$/', $url, $matches) === 1) {
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => $matches[1],
                            'data' => $matches[2],
                        ],
                    ];
                }
            }
        }

        return $parts;
    }
}
