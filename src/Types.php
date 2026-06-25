<?php

declare(strict_types=1);

namespace RunApi\GptImage;

/**
 * Constants for model slugs supported by the GPT Image PHP SDK.
 */
final class Types
{
    /** @var list<string> */
    public const TEXT_TO_IMAGE_MODELS = ['gpt-image-1.5'];

    /** @var list<string> */
    public const EDIT_IMAGE_MODELS = ['gpt-image-1.5'];

    private function __construct()
    {
    }
}
