# GPT Image PHP SDK for RunAPI

[![Packagist](https://img.shields.io/packagist/v/runapi-ai/gpt-image)](https://packagist.org/packages/runapi-ai/gpt-image)
[![License](https://img.shields.io/github/license/runapi-ai/gpt-image-php)](https://github.com/runapi-ai/gpt-image-php/blob/main/LICENSE)

The GPT Image PHP SDK is the language-specific package for GPT Image
on RunAPI. Use this package when your application needs Composer installs,
associative-array request bodies, task status lookup, and consistent RunAPI
errors in PHP.

This README is the PHP package guide for the public `gpt-image-php` split
repository. For model details, use https://runapi.ai/models/gpt-image; for API
reference, use https://runapi.ai/docs/api/gpt-image/text-to-image; for SDK docs, use
https://runapi.ai/docs/resources/sdks.

## Install

```bash
composer require runapi-ai/gpt-image
```

## Quick start

```php
<?php

require __DIR__ . "/vendor/autoload.php";

use RunApi\GptImage\GptImageClient;

$client = new GptImageClient(); // reads RUNAPI_API_KEY

$editImageTask = $client->editImage->create([
    'model' => 'gpt-image-1.5',
    'aspect_ratio' => '1:1',
    'prompt' => 'Make it golden hour',
    'quality' => 'medium',
    'source_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
]);

$task = $client->textToImage->create([
    'model' => 'gpt-image-1.5',
    'aspect_ratio' => '1:1',
    'prompt' => 'A precise product render on white marble',
    'quality' => 'medium',
]);

$status = $client->textToImage->get($task->id);

$result = $client->textToImage->run([
    'model' => 'gpt-image-1.5',
    'aspect_ratio' => '1:1',
    'prompt' => 'A serene mountain lake at dawn',
    'quality' => 'medium',
]);

echo $result->images[0]->url . PHP_EOL;
```

Use `create()` to submit a task and return quickly, `get()` to fetch the latest
task state, and `run()` when a script should create and poll until completion.
In web request handlers, prefer `create()` plus webhook or later `get()`
polling so a worker is not held open.


RunAPI-generated file URLs are temporary. Download and store generated files
in your own durable storage within the retention window; do not treat returned
URLs as long-term assets.

## Language notes

Pass request parameters as associative arrays with snake_case keys. The
available resources are `textToImage`, `editImage`. Keep `RUNAPI_API_KEY` in the environment
or your secret manager; never commit API keys or callback secrets.

## Links

- Model page: https://runapi.ai/models/gpt-image
- SDK docs: https://runapi.ai/docs/resources/sdks
- Product docs: https://runapi.ai/docs/api/gpt-image/text-to-image
- Pricing and rate limits: https://runapi.ai/models/gpt-image
- Full catalog: https://runapi.ai/models
- GitHub repository: https://github.com/runapi-ai/gpt-image-php
- Multi-language SDK repository: https://github.com/runapi-ai/gpt-image-sdk

## License

Licensed under the Apache License, Version 2.0.
