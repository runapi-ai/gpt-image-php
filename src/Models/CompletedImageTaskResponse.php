<?php

declare(strict_types=1);

namespace RunApi\GptImage\Models;

use RunApi\Core\Support\Payload;

/**
 * Completed image task response returned by run(); outputs are guaranteed present.
 */
readonly class CompletedImageTaskResponse extends ImageTaskResponse
{
    /**
     * Hydrate a completed image task response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(id: Payload::string($raw, 'id'), status: Payload::string($raw, 'status'), error: self::error($raw), images: self::images($raw, required: true), raw: $raw);
    }

    /**
     * Narrow a polled task response after completion has been confirmed.
     */
    public static function fromResponse(ImageTaskResponse $response): self
    {
        return self::fromArray($response->toArray());
    }
}
