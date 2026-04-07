<?php

namespace App\Traits;

trait NormalizesPhone
{
    /**
     * Normalize phone number to format 62xxx
     */
    public function normalizePhoneNumber($phone): ?string
    {
        if (empty($phone)) return null;

        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) return null;

        // Replace 08... with 628...
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Prepend 62 if it starts with 8...
        if (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
