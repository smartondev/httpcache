<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Matchers\MatcherHeaderAbstract;

it('test headers', function(array $input, array $expected) {
    $headersMock = Mockery::mock(MatcherHeaderAbstract::class)->makePartial();
    $headersMock->headers($input);

    $headersMock->shouldAllowMockingProtectedMethods();
    $headersMock->shouldReceive('getHeaders')->passthru();
    expect($headersMock->getHeaders())->toBe($expected);
})->with([
    'lowercase' => [
        ['etag' => '"123"'],
        ['etag' => '"123"'],
    ],
    'one' => [
        ['ETag' => '"123"'],
        ['etag' => '"123"'],
    ],
    'two' => [
        ['ETag' => '"123"', 'Cache-Control' => 'no-cache'],
        ['etag' => '"123"', 'cache-control' => 'no-cache'],
    ],
    'nested array' => [
        ['array' => ['123', '456']],
        ['array' => ['123', '456']],
    ],
]);

it('reset headers', function(array $input) {
    $headersMock = Mockery::mock(MatcherHeaderAbstract::class)->makePartial();
    $headersMock->headers($input);
    $headersMock->resetHeaders();

    $headersMock->shouldAllowMockingProtectedMethods();
    $headersMock->shouldReceive('getHeaders')->passthru();
    expect($headersMock->getHeaders())->toBeEmpty();
})->with([
    'empty' => [[]],
    'one' => [['ETag' => '"123"']],
    'two' => [['ETag' => '"123"', 'Cache-Control' => 'no-cache']],
    'nested array' => [['array' => ['123', '456']]],
]);

it('with headers', function(array $input, array $add, array $expected) {
    $headersMock = Mockery::mock(MatcherHeaderAbstract::class)->makePartial();
    $headersMock->headers($input);
    $headersMock = $headersMock->withHeaders($add);

    $headersMock->shouldAllowMockingProtectedMethods();
    $headersMock->shouldReceive('getHeaders')->passthru();
    expect($headersMock->getHeaders())->toBe($expected);
})->with([
    'empty' => [
        [],
        ['ETag' => '"123"'],
        ['etag' => '"123"'],
    ],
    'one' => [
        ['ETag' => '"123"'],
        ['Cache-Control' => 'no-cache'],
        ['cache-control' => 'no-cache'],
    ],
    'two' => [
        ['ETag' => '"123"', 'Cache-Control' => 'no-cache'],
        ['Cache-Control' => 'no-store'],
        ['cache-control' => 'no-store'],
    ],
    'nested array' => [
        ['array' => ['123', '456']],
        ['Foo' => ['Bar','Baz']],
        ['foo' => ['Bar','Baz']],
    ],
]);

it('without headers', function(array $input) {
    $headersMock = Mockery::mock(MatcherHeaderAbstract::class)->makePartial();
    $headersMock->headers($input);
    $headersMock = $headersMock->withoutHeaders();

    $headersMock->shouldAllowMockingProtectedMethods();
    $headersMock->shouldReceive('getHeaders')->passthru();
    expect($headersMock->getHeaders())->toBeEmpty();
})->with([
    'empty' => [[]],
    'one' => [['ETag' => '"123"']],
    'two' => [['ETag' => '"123"', 'Cache-Control' => 'no-cache']],
    'nested array' => [['array' => ['123', '456']]],
]);