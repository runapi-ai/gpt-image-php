<?php

declare(strict_types=1);

namespace RunApi\GptImage\Models;

use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * Generated image file metadata.
 */
readonly class Image extends BaseModel
{
    /**
     * Create an image value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(public string $url, array $raw = [])
    {
        parent::__construct($raw === [] ? ['url' => $url] : $raw);
    }

    /**
     * Hydrate an image from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(url: Payload::string($raw, 'url'), raw: $raw);
    }
}
