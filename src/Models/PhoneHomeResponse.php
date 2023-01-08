<?php

namespace SoftwarePunt\PhoneHome\Models;

use Psr\Http\Message\StreamInterface;

class PhoneHomeResponse extends BaseModel
{
    /**
     * Indicates whether the phone home request was accepted and processed successfully.
     */
    public bool $ok = false;

    /**
     * Information about the active SLA, if any.
     */
    public ?ServiceLevelAgreement $sla = null;

    // -----------------------------------------------------------------------------------------------------------------
    // Parse util

    public static function tryParseFromStream(StreamInterface $stream): ?PhoneHomeResponse
    {
        return self::tryParse($stream->getContents());
    }

    public static function tryParse(string $contents): ?PhoneHomeResponse
    {
        $json = @json_decode($contents, associative: true);
        if (empty($json) || !is_array($json))
            // JSON parse error
            return null;

        return new PhoneHomeResponse($json);
    }
}