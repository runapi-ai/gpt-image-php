<?php

declare(strict_types=1);

namespace RunApi\GptImage\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\GptImage\GptImageClient;
use RunApi\GptImage\Models\CompletedImageTaskResponse;
use RunApi\GptImage\Resources\EditImage;
use RunApi\GptImage\Resources\TextToImage;

final class GptImageClientTest extends TestCase
{
    public function testExposesTypedResources(): void
    {
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(TextToImage::class, $client->textToImage);
        self::assertInstanceOf(EditImage::class, $client->editImage);
    }

    public function testCreatePostsCompactedBodyToCorrectPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
        ]);
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $task = $client->textToImage->create([
            'model' => 'gpt-image-1.5',
            'prompt' => 'A product render',
            'aspect_ratio' => '1:1',
            'quality' => 'medium',
            'callback_url' => '',
            'seed' => null,
        ]);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('task_1', $task->id);
        self::assertSame('/api/v1/gpt_image/text_to_image', $transport->requests[0]->getUri()->getPath());
        self::assertSame('gpt-image-1.5', $body['model']);
        self::assertSame('1:1', $body['aspect_ratio']);
        self::assertSame('medium', $body['quality']);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('seed', $body);
    }

    public function testRunReturnsTypedCompletedResponseAndPreservesUnknownFields(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","images":[{"url":"https://file.runapi.ai/result"}],"extra_field":"kept"}'),
        ]);
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->textToImage->run([
            'model' => 'gpt-image-1.5',
            'prompt' => 'A product render',
            'aspect_ratio' => '1:1',
            'quality' => 'medium',
        ]);

        self::assertInstanceOf(CompletedImageTaskResponse::class, $result);
        self::assertSame('https://file.runapi.ai/result', $result->images[0]->url);
        self::assertSame('kept', $result->toArray()['extra_field']);
        self::assertSame('/api/v1/gpt_image/text_to_image/task_1', $transport->requests[1]->getUri()->getPath());
    }

    public function testCompletedResponseRequiresResultFiles(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed"}'),
        ]);
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('images is required');

        $client->textToImage->run([
            'model' => 'gpt-image-1.5',
            'prompt' => 'A product render',
            'aspect_ratio' => '1:1',
            'quality' => 'medium',
        ]);
    }

    public function testTextToImageRequiresAspectRatioAndQuality(): void
    {
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        try {
            $this->invokeCreate($client->textToImage, [
                'model' => 'gpt-image-1.5',
                'prompt' => 'A product render',
                'quality' => 'medium',
            ]);
            self::fail('Expected missing aspect_ratio to fail before HTTP.');
        } catch (ValidationException $exception) {
            self::assertSame('aspect_ratio is required', $exception->getMessage());
        }

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('quality is required');

        $this->invokeCreate($client->textToImage, [
            'model' => 'gpt-image-1.5',
            'prompt' => 'A product render',
            'aspect_ratio' => '1:1',
        ]);
    }

    public function testTextToImageRejectsInvalidAspectRatioAndQuality(): void
    {
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        try {
            $this->invokeCreate($client->textToImage, [
                'model' => 'gpt-image-1.5',
                'prompt' => 'A product render',
                'aspect_ratio' => '16:9',
                'quality' => 'medium',
            ]);
            self::fail('Expected invalid aspect_ratio to fail before HTTP.');
        } catch (ValidationException $exception) {
            self::assertSame('aspect_ratio must be one of the allowed values', $exception->getMessage());
        }

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('quality must be one of the allowed values');

        $this->invokeCreate($client->textToImage, [
            'model' => 'gpt-image-1.5',
            'prompt' => 'A product render',
            'aspect_ratio' => '1:1',
            'quality' => 'ultra',
        ]);
    }


    public function testSecondaryResourceUsesItsOwnPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_2"}'),
        ]);
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->editImage->create([
            'model' => 'gpt-image-1.5',
            'prompt' => 'A product render',
            'source_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
            'aspect_ratio' => '1:1',
            'quality' => 'medium',
        ]);

        self::assertSame('/api/v1/gpt_image/edit_image', $transport->requests[0]->getUri()->getPath());
    }

    public function testEditImageRequiresAspectRatioAndQuality(): void
    {
        $client = new GptImageClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        try {
            $this->invokeCreate($client->editImage, [
                'model' => 'gpt-image-1.5',
                'prompt' => 'A product render',
                'source_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
                'quality' => 'medium',
            ]);
            self::fail('Expected missing aspect_ratio to fail before HTTP.');
        } catch (ValidationException $exception) {
            self::assertSame('aspect_ratio is required', $exception->getMessage());
        }

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('quality is required');

        $this->invokeCreate($client->editImage, [
            'model' => 'gpt-image-1.5',
            'prompt' => 'A product render',
            'source_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
            'aspect_ratio' => '1:1',
        ]);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function invokeCreate(object $resource, array $params): mixed
    {
        return (new \ReflectionMethod($resource, 'create'))->invoke($resource, $params);
    }
}
