<?php

declare(strict_types=1);

namespace ApiClient\Tests;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;

abstract class ServiceTest extends TestCase
{
    protected function createClientMock(int $responseStatusCode, string $fileName): Client
    {

        $mock = new MockHandler([new Response($responseStatusCode, [], file_get_contents(__DIR__."/responses/{$fileName}"))]);
        $handler = HandlerStack::create($mock);

        return $client = new Client(['handler' => $handler]);
    }
}
