<?php

namespace App\Exceptions;

use RuntimeException;

class AiServiceException extends RuntimeException
{
    public static function notConfigured(): self
    {
        return new self('Layanan AI belum dikonfigurasi. Isi API key AI pada file .env sesuai provider yang dipilih, lalu coba lagi.');
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public static function fromResponse(string $operation, int $status, ?array $payload = null): self
    {
        $providerMessage = is_array($payload) ? data_get($payload, 'error.message') : null;
        $providerMessage = is_string($providerMessage) ? trim($providerMessage) : '';

        $message = match (true) {
            $status === 401 => 'Kunci API AI ditolak. Periksa konfigurasi API key pada file .env.',
            $status === 429 => 'Kuota API tercapai atau habis. Coba lagi nanti atau gunakan provider lain.',
            $status >= 500 => 'Layanan AI sedang bermasalah. Coba lagi nanti.',
            $providerMessage !== '' => 'Layanan AI menolak permintaan ('.$operation.'): '.$providerMessage,
            default => 'Permintaan ke layanan AI gagal ('.$operation.', HTTP '.$status.').',
        };

        return new self($message);
    }
}
