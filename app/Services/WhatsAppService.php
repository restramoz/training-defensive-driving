<?php

namespace App\Services;

class WhatsAppService
{
    /**
     * Check if WhatsApp integration is actively enabled with a valid number.
     */
    public function isEnabled(): bool
    {
        $enabled = (bool) config('training.whatsapp.enabled', false);
        if (! $enabled) {
            return false;
        }

        $normalized = $this->normalize(config('training.whatsapp.number'));

        return ! empty($normalized);
    }

    /**
     * Normalize phone number to international E.164-compatible format without '+' or special characters.
     */
    public function normalize(?string $number = null): ?string
    {
        $raw = $number ?? (string) config('training.whatsapp.number', '');
        $digits = preg_replace('/\D+/', '', $raw);

        if (empty($digits)) {
            return null;
        }

        // Convert Indonesian leading zero (e.g. 0812...) to 62812...
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        // Validate length reasonable for phone numbers (at least 8 digits)
        if (strlen($digits) < 8) {
            return null;
        }

        return $digits;
    }

    /**
     * Generate safe, encoded wa.me URL or null if disabled.
     */
    public function getUrl(?string $message = null): ?string
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $number = $this->normalize();
        if (! $number) {
            return null;
        }

        $text = $message ?? (string) config('training.whatsapp.default_message', '');

        if (! empty(trim($text))) {
            return 'https://wa.me/'.$number.'?text='.rawurlencode(trim($text));
        }

        return 'https://wa.me/'.$number;
    }

    /**
     * Get pre-filled message for specific audience contexts.
     */
    public function getIndividualUrl(): ?string
    {
        return $this->getUrl(config('training.whatsapp.individual_message'));
    }

    /**
     * Get pre-filled message for corporate and fleet inquiries.
     */
    public function getCorporateUrl(): ?string
    {
        return $this->getUrl(config('training.whatsapp.corporate_message'));
    }
}
