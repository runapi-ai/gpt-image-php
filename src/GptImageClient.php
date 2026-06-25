<?php

declare(strict_types=1);

namespace RunApi\GptImage;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\GptImage\Resources\EditImage;
use RunApi\GptImage\Resources\TextToImage;

/**
 * The GPT Image 1.5 image generation API client.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
final class GptImageClient extends BaseClient
{
    /**
     * Provides text-to-image generation operations.
     */
    public readonly TextToImage $textToImage;
    /**
     * Provides image edit operations.
     */
    public readonly EditImage $editImage;

    /**
     * Create a GPT Image client with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->textToImage = TextToImage::fromHttp($this->http);
        $this->editImage = EditImage::fromHttp($this->http);
    }
}
