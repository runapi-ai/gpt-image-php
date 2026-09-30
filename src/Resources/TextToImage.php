<?php

declare(strict_types=1);

namespace RunApi\GptImage\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\GptImage\Models\CompletedImageTaskResponse;
use RunApi\GptImage\Models\ImageTaskResponse;

/**
 * Handles text-to-image generation tasks.
 */
readonly class TextToImage extends TypedConfiguredResource
{
    /**
     * Submits a GPT Image text-to-image task and returns immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   aspect_ratio: '1:1'|'2:3'|'3:2',
     *   quality: 'medium'|'high',
     *   callback_url?: string
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Fetches the current status of a GPT Image text-to-image task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): ImageTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var ImageTaskResponse $response */
        return $response;
    }

    /**
     * Submits a GPT Image text-to-image task and polls until it completes.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   aspect_ratio: '1:1'|'2:3'|'3:2',
     *   quality: 'medium'|'high',
     *   callback_url?: string
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedImageTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedImageTaskResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/gpt_image/text_to_image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
            'text-to-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
        );
    }
}
